# Idempotency Protection Guide for POS System

## What is Idempotency? (The "Double-Click Problem")

**Idempotency** means: *making the same request multiple times has the same effect as making it once.*

**Your scenario:** A cashier clicks "Pay" button twice quickly (or network retries). Without protection, this could:
- Charge the customer twice
- Create duplicate sale records
- Decrement stock twice
- Create duplicate payment transactions

---

## Where to Add Protection in Your Codebase

### 1. PaymentTransaction Model - Add Unique Constraint (Database Level)

**File:** `app/Models/PaymentTransaction.php` (but actually needs a migration)

```php
// TODO: Create a migration to add a UNIQUE INDEX on gateway_reference
// This is the STRONGEST protection - database will reject duplicates
// 
// Run: php spark make:migration AddUniqueGatewayReferenceToPaymentTransactions
// Then in the migration file:
// $this->forge->addUniqueKey('gateway_reference', true); // true = unique index
// 
// Why: Even if PHP code fails, database guarantees no duplicate gateway_reference
```

### 2. PaymentTransaction Model - Add Idempotency Check Method

**File:** `app/Models/PaymentTransaction.php` - Add this method:

```php
/**
 * Check if a transaction with this gateway_reference already exists.
 * 
 * IDEMPOTENCY PROTECTION: Prevents processing the same payment twice.
 * Call this BEFORE inserting a new transaction.
 * 
 * @param string $gateway_reference Unique reference from payment gateway
 * @return array|null Existing transaction data or null if not found
 */
public function findByGatewayReference(string $gateway_reference): ?array
{
    return $this->where('gateway_reference', $gateway_reference)->first();
}

/**
 * Atomically create a transaction ONLY if gateway_reference doesn't exist.
 * 
 * IDEMPOTENCY PROTECTION: Uses "INSERT ... ON DUPLICATE KEY UPDATE" pattern
 * or checks-then-inserts with a database lock.
 * 
 * Returns: [transaction_id, is_new] where is_new = true if created, false if existed
 * 
 * @param array $data Transaction data including gateway_reference
 * @return array [int transaction_id, bool is_new]
 */
public function createIdempotent(array $data): array
{
    // Try to find existing first
    $existing = $this->findByGatewayReference($data['gateway_reference']);
    if ($existing) {
        // Already exists - return existing ID, mark as not new
        return [$existing['transaction_id'], false];
    }
    
    // Doesn't exist - try to insert
    $transaction_id = $this->insert($data);
    if ($transaction_id === false) {
        // Insert failed - maybe race condition? Check again
        $existing = $this->findByGatewayReference($data['gateway_reference']);
        if ($existing) {
            return [$existing['transaction_id'], false];
        }
        throw new \RuntimeException('Failed to create payment transaction: ' . implode(', ', $this->errors()));
    }
    
    return [$transaction_id, true];
}
```

### 3. Newsales Controller - process_payment() Method

**File:** `app/Controllers/Newsales.php` - Around line 600-650

**CURRENT CODE (has soft check only):**
```php
// Current code does a "soft check" but continues anyway:
$existing = $payment_transaction_model
    ->where('gateway_reference', $gateway_reference)
    ->first();

if ($existing) {
    // Only logs a note, doesn't block!
    error_log('[PAYMENT] SOFT NOTE: gateway_reference already used...');
    // CONTINUES to insert anyway - THIS IS THE BUG
}
```

**REPLACE WITH (strong idempotency):**
```php
// ============================================================
// IDEMPOTENCY PROTECTION - Start
// ============================================================
// 
// PROBLEM: Cashier double-clicks "Pay" or network retries
// SOLUTION: Check if gateway_reference already processed
//           If YES: Return existing result, don't process again
//           If NO:  Process normally
// 
// WHY gateway_reference? It's unique per payment attempt from gateway
// (e.g., Stripe payment_intent_id, M-Pesa CheckoutRequestID)
// ============================================================

$payment_transaction_model = new PaymentTransaction();

// Use the new idempotent creation method
try {
    [$transaction_id, $is_new] = $payment_transaction_model->createIdempotent($transaction_data);
    
    if (!$is_new) {
        // ========================================================
        // IDEMPOTENT RESPONSE: Transaction already exists
        // ========================================================
        // This means the payment was ALREADY processed (or is processing)
        // We should return the SAME result as the first request
        // 
        // LOGIC: Load the existing transaction and dispatch based on ITS status
        // ========================================================
        
        error_log('[PAYMENT] IDEMPOTENT: gateway_reference already processed, transaction_id=' . $transaction_id);
        
        $existing_transaction = $payment_transaction_model->find($transaction_id);
        
        // Store in session for consistency
        session()->set('transaction_id', $transaction_id);
        
        // Dispatch based on EXISTING transaction status (not new request status)
        switch ($existing_transaction['transaction_status']) {
            case 'SUCCESS':
                return $this->payment_success();
            case 'FAILED':
                return $this->payment_failed();
            case 'PENDING':
                return $this->payment_pending();
            case 'CANCELLED':
            default:
                return $this->payment_cancelled();
        }
    }
    
    // ========================================================
    // NEW TRANSACTION: Continue with normal flow
    // ========================================================
    error_log('[PAYMENT] NEW transaction created: ' . $transaction_id);
    
} catch (\Throwable $e) {
    error_log('[PAYMENT] ERROR in idempotent create: ' . $e->getMessage());
    session()->remove('sale_id');
    session()->remove('transaction_id');
    return redirect()->to('/newsales')->with('error', 'Payment processing error');
}

// Store transaction_id in session for downstream methods
session()->set('transaction_id', $transaction_id);

// Continue with existing switch statement...
switch ($transaction_status) {
    case 'SUCCESS':
        return $this->payment_success();
    // ... rest of cases
}
```

### 4. Newsales Controller - payment_success() Method

**File:** `app/Controllers/Newsales.php` - Around line 699+

**ADD at the START of payment_success():**

```php
// ============================================================
// IDEMPOTENCY PROTECTION - payment_success()
// ============================================================
// 
// PROBLEM: If user refreshes page or retries, this runs again
// SOLUTION: Check if sale is ALREADY paid before doing anything
// ============================================================

$sale_model = new SaleModel();
$sale = $sale_model->find($sale_id);

if (!$sale) {
    return redirect()->to('/newsales')->with('error', 'Sale not found');
}

// IDEMPOTENCY CHECK: If already paid, don't process again
if ($sale['status'] === 'PAID') {
    error_log('[PAYMENT-SUCCESS] IDEMPOTENT: Sale ' . $sale_id . ' already PAID, skipping');
    
    // Still need to clean up session
    session()->remove('sale_id');
    session()->remove('cart');
    session()->remove('total');
    session()->remove('transaction_id');
    
    // Redirect to receipt with success message
    return redirect()->to('/receipt/view/' . $sale_id)
        ->with('message', 'Payment already processed. Showing receipt.');
}

// Continue with existing payment_success logic...
$db = \Config\Database::connect();
$db->transStart();
// ... rest of method
```

### 5. PaymentTransaction Table - Add Unique Index (Migration)

**Create new migration file:** `app/Database/Migrations/xxxx_AddUniqueGatewayReference.php`

```php
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUniqueGatewayReference extends Migration
{
    public function up()
    {
        // ============================================================
        // IDEMPOTENCY: Unique index on gateway_reference
        // ============================================================
        // 
        // This is the ULTIMATE protection. Even if PHP code has bugs,
        // the DATABASE will reject duplicate gateway_reference inserts.
        // 
        // NOTE: Run this AFTER cleaning up any existing duplicates!
        // ============================================================
        
        $this->forge->addUniqueKey('gateway_reference', true);
        // Or if using raw SQL for existing table:
        // $this->db->query('ALTER TABLE paymenttransactions ADD UNIQUE KEY unique_gateway_ref (gateway_reference)');
    }

    public function down()
    {
        $this->forge->dropKey('unique_gateway_ref');
    }
}
```

---

## Summary: What Changes Where

| File | Change Type | Priority |
|------|-------------|----------|
| `app/Database/Migrations/...` | Add unique index on `gateway_reference` | **CRITICAL** - Database level |
| `app/Models/PaymentTransaction.php` | Add `findByGatewayReference()` and `createIdempotent()` | **HIGH** - Model level |
| `app/Controllers/Newsales.php` - `process_payment()` | Use `createIdempotent()`, handle existing transaction | **HIGH** - Controller level |
| `app/Controllers/Newsales.php` - `payment_success()` | Check `sale['status'] === 'PAID'` at start | **HIGH** - Controller level |

---

## How to Test

1. **Manual test:** Click "Pay" button twice rapidly
2. **Automated test:** Send two identical POST requests to `/newsales/payment` with same `gateway_reference`
3. **Verify:** Only ONE payment record created, stock decremented ONCE, sale status = PAID

---

## Key Concepts for Junior Developers

| Concept | Simple Explanation |
|---------|-------------------|
| **Idempotency** | "Do it once, or do it 100 times - result is the same" |
| **Race Condition** | Two requests at same time both pass the "check" before either "inserts" |
| **Unique Index** | Database rule: "This column must have unique values" - enforced by DB |
| **Atomic Operation** | Check + Insert as ONE operation that can't be interrupted |
| **Gateway Reference** | Unique ID from payment provider (Stripe, M-Pesa, etc.) - use THIS for dedup |

---

## Quick Reference: Code Comments to Add

Copy these comment blocks into your code as you implement:

```php
// ============================================================
// IDEMPOTENCY PROTECTION: [Description of what this protects]
// ============================================================
// PROBLEM: [What goes wrong without this]
// SOLUTION: [What this code does]
// WHY: [Why this approach works]
// ============================================================
```

Use this template everywhere you add protection!