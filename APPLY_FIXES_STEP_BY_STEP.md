# 📝 Step-by-Step: How to Apply Fixes to Your Code

This guide walks you through EXACTLY what to copy and paste to fix your code.

---

## 🎯 What Needs Fixing

Your code has alignment issues between:
1. **Routes** - payment endpoint missing
2. **Controller** - methods not organized properly
3. **View** - form fields missing

---

## 🔧 STEP 1: Fix Routes (CRITICAL)

**File:** `app/Config/Routes.php`

**What to do:**
1. Open `app/Config/Routes.php`
2. Find this line:
```php
$routes->post('newsales/checkout', 'Newsales::checkout');
```

3. Add this line RIGHT AFTER it:
```php
$routes->post('newsales/payment', 'Newsales::payment');  // Fixed: Added missing payment route
```

**After:**
```php
$routes->post('newsales/clear', 'Newsales::clear');
$routes->post('newsales/checkout', 'Newsales::checkout');
$routes->post('newsales/payment', 'Newsales::payment');  // Fixed: Added missing payment route
```

✅ **SAVE**

---

## 🔧 STEP 2: Fix Controller (COMPLEX - Multiple Changes)

**File:** `app/Controllers/newsales.php`

This file needs major changes. I recommend:

### OPTION A: Copy Entire Fixed File (Easier)
```bash
1. Download or copy newsales_FIXED.php
2. Backup your current newsales.php
3. Replace newsales.php with newsales_FIXED.php
4. Rename if needed
```

### OPTION B: Manual Edits (If you want to keep your current structure)

#### Change #1: Fix Newsale() method (around line 50-180)

**REPLACE THIS:**
```php
$session = session();

/**
 * ISSUE 1: Duplicate sale_number with rand(10, 100)
 * ... (lots of comments)
 */
$total = 0;
$cart = $session->get('cart') ?? [];

if(!empty($cart)){
    // ... complex code
}
else{
    return redirect()->back()->with('erro','cart not found');
}
// ... stock reduction code
// ... payment transaction code
```

**WITH THIS:**
```php
$session = session();
$cart = $session->get('cart') ?? [];

// Check if cart is empty before creating sale
if(empty($cart)){
    return redirect()->to('/newsales')->with('message','Cart is empty. Add items before creating sale.');
}

// Calculate total from cart items (don't trust session)
$total = 0;
foreach($cart as $item) { 
    $total += $item['total']; 
}

// Store total in session for payment later
$session->set('total', $total);

// Create the sale record (status = PENDING until payment succeeds)
$sale_id = $sale_model->insert([
    'sale_number'      => date('YmdHis') . mt_rand(1000, 9999),
    'status'           => 'PENDING',
    'total_amount'     => $total,
    'sale_date'        => date('Y-m-d H:i:s'),
    'handled_by_user'  => $session->get('username'),
]);

// Store sale_id in session for payment method to use
$session->set('sale_id', $sale_id);

// Reduce product stock and create sale items
foreach($cart as $item){
    $product = $product_model->find($item['product_id']);
    $current_stock = $product['stock_quantity'];
    $new_stock = $current_stock - $item['qty'];
    
    if($new_stock < 0){
        return redirect()->back()->with('message','Insufficient stock for ' . $item['product_name']);
    }
    
    $product_model->update($item['product_id'], ['stock_quantity' => $new_stock]);
    
    $sale_item_model->insert([
        'sale_id'      => $sale_id,
        'product_id'   => $item['product_id'],
        'quantity'     => $item['qty'],
        'unite_price'  => $item['price'],
        'subtotal'     => $item['total'],
        'added_by_user'=> $session->get('username')
    ]);
}

// Return view with products, cart, and username
return view('DashBoard/newsales', [
    'username'      => $user,
    'product'       => $products,
    'cart'          => $cart,
    'total'         => $total,  // Pass total to view so modal can display it
]);
```

#### Change #2: Fix payment() method (around line 191)

**REPLACE THIS:**
```php
public function payment(){
    /**
     * ALIGNMENT ISSUE 1: Missing route definition
     * ... (lots of comments)
     */
    
    $session=session();
    
    if (!$session->get('sale_id') || !$session->get('transaction_id')) {
            return redirect()->to('/newsales')->with('error', 'Invalid sale session. Start new sale.');
    }
    $transaction_id=$session->get('transaction_id');
    $sale_id=$session->get('sale_id');
    
    // ... old code that updates existing transaction
    $payment_transaction_model = new PaymentTransaction();
    $payment_transaction_model->update($transaction_id, [
        'payment_method'     => $payment_method,
        'transaction_status' => $transaction_status,
        'gateway_reference'  => $gateway_reference,
        'amount_attempted'   => $amount,
    ]);
```

**WITH THIS:**
```php
public function payment(){
    // Fixed: Route is now added to Routes.php
    // Fixed: Form now includes transaction_status field
    
    $session = session();
    $sale_id = $session->get('sale_id');
    $total = $session->get('total');
    
    // Validate that we have a valid sale
    if(!$sale_id || !$total){
        return redirect()->to('/newsales')->with('message', 'No sale found. Start a new sale.');
    }
    
    // Get payment data from form
    $payment_method     = $this->request->getPost('payment_method');
    $transaction_status = $this->request->getPost('transaction_status');
    $gateway_reference  = $this->request->getPost('gateway_reference');
    
    // Validate form data
    if(empty($payment_method) || empty($transaction_status) || empty($gateway_reference)){
        return redirect()->back()->with('message', 'Please fill all payment fields.');
    }
    
    // Create payment transaction record (Fixed: moved from Newsale() method)
    $payment_transaction_model = new PaymentTransaction();
    $transaction_id = $payment_transaction_model->insert([
        'sale_id'            => $sale_id,
        'payment_method'     => $payment_method,
        'transaction_status' => $transaction_status,
        'gateway_reference'  => $gateway_reference,
        'amount_attempted'   => $total,
    ]);

    // Handle payment result
    if ($transaction_status === 'SUCCESS') {
        $payment_model = new PaymentModel();
        $payment_model->insert([
            'sale_id'          => $sale_id,
            'payment_method'   => $payment_method,
            'payment_status'   => $transaction_status,
            'processed_by_user'=> $session->get('username'),
            'payment_date'     => date('Y-m-d H:i:s'),
        ]);

        $sale_model = new SaleModel();
        $sale_model->update($sale_id, ['status'=>'PAID']);
        
        // Clear cart from session after payment succeeds
        $session->remove('cart');
        $session->remove('total');
        $session->remove('sale_id');
        
        return redirect()->to('/newsales')->with('message','Payment successful! Sale completed.');
    }
    else if($transaction_status === "FAILED"){
        $sale_model = new SaleModel();
        $sale_model->update($sale_id, ['status'=>'FAILED']);
        return redirect()->to('/newsales')->with('message','Payment Status FAILED');
    }
    else {
        return redirect()->to('/newsales')->with('message','Payment status: ' . $transaction_status);
    }
}
```

✅ **SAVE**

---

## 🔧 STEP 3: Fix View (Simple Changes)

**File:** `app/Views/Dashboard/newsales.php`

### Change: Fix Payment Modal Form

**Find this section** (around line 296-315):
```html
<!-- Payment Popup Modal -->
<div id="paymentModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Payment Information</h2>
    
    <!-- MISSING: Display cart total here -->
    <!-- TODO: Add: <p>...</p> -->
    
    <form method="post" action="<?= base_url('newsales/payment') ?>">
      <?= csrf_field() ?>
      
      <label for="payment_method">Payment Method</label>
      <select name="payment_method" id="payment_method" required>
        <option value="mobileMoney">Mobile Money</option>
        <option value="card">Card</option>
        <option value="cash">Cash</option>
        <option value="bank_transfer">Bank Transfer</option>
      </select>

      <label for="gateway_reference">Gateway Reference</label>
      <input type="text" name="gateway_reference" id="gateway_reference" placeholder="Enter reference" required>

      <!-- MISSING FIELD: transaction_status -->
      <!-- TODO: Add one of these... -->

      <button type="submit" class="pay-btn">Confirm Payment</button>
    </form>
  </div>
</div>
```

**REPLACE WITH THIS:**
```html
<!-- Payment Popup Modal -->
<div id="paymentModal" class="modal">
  <div class="modal-content">
    <span class="close">&times;</span>
    <h2>Payment Information</h2>
    
    <!-- Fixed: Display total amount customer must pay -->
    <p style="font-size: 18px; font-weight: bold; color: #0A5741;">
      Total Amount: K<?php echo isset($total) ? number_format($total, 2) : '0.00'; ?>
    </p>
    
    <form method="post" action="<?= base_url('newsales/payment') ?>">
      <?= csrf_field() ?>
      
      <label for="payment_method">Payment Method</label>
      <select name="payment_method" id="payment_method" required>
        <option value="mobileMoney">Mobile Money</option>
        <option value="card">Card</option>
        <option value="cash">Cash</option>
        <option value="bank_transfer">Bank Transfer</option>
      </select>

      <!-- Fixed: Added transaction_status field (required by controller) -->
      <label for="transaction_status">Payment Status</label>
      <select name="transaction_status" id="transaction_status" required>
        <option value="SUCCESS">Success</option>
        <option value="FAILED">Failed</option>
        <option value="PENDING">Pending</option>
      </select>

      <label for="gateway_reference">Gateway Reference</label>
      <input type="text" name="gateway_reference" id="gateway_reference" placeholder="Enter reference" required>

      <button type="submit" class="pay-btn">Confirm Payment</button>
    </form>
  </div>
</div>
```

✅ **SAVE**

---

## ✅ Verification Checklist

After making changes, verify:

- [ ] Routes.php has payment route
- [ ] Newsale() method validates cart first
- [ ] Newsale() passes `$total` to view
- [ ] payment() creates PaymentTransaction (not updates it)
- [ ] Payment modal shows total amount
- [ ] Payment modal has transaction_status field
- [ ] No error messages about "undefined variable"

---

## 🧪 Test Scenarios

### Test 1: Add to Cart
```
1. Click "Add to Cart" on any product
2. Item should appear in cart table
3. Total should update
✓ Pass if cart shows item with correct qty and total
```

### Test 2: Checkout
```
1. Click "Checkout" button
2. Payment modal should appear
3. Modal should show total amount
✓ Pass if modal displays and total is correct
```

### Test 3: Payment SUCCESS
```
1. Fill payment form:
   - Payment Method: Mobile Money
   - Payment Status: SUCCESS
   - Gateway Reference: test123
2. Click Confirm Payment
3. Should see "Payment successful!" message
4. Cart should be cleared
✓ Pass if all above happen and database Sale status = PAID
```

### Test 4: Payment FAILED
```
1. Fill payment form:
   - Payment Method: Card
   - Payment Status: FAILED
   - Gateway Reference: test456
2. Click Confirm Payment
3. Should see "Payment failed!" message
4. Cart should remain (for retry)
✓ Pass if database Sale status = FAILED and cart stays
```

---

## 🆘 Common Issues & Solutions

| Issue | Cause | Solution |
|-------|-------|----------|
| 404 error when clicking "Confirm Payment" | Missing payment route | Add route to Routes.php |
| "Please fill all fields" error | Missing transaction_status | Add transaction_status select to form |
| Total shows "0.00" in modal | $total not passed to view | Add `'total' => $total` to view return |
| "Undefined variable" error | Missing username/total | Pass all required vars to view |
| Stock not reducing | Code not implemented | Ensure foreach loop in Newsale() |
| Cart not clearing | Clear code in wrong place | Use payment() method, not Newsale() |

---

## 📞 Need Help?

If you get stuck:
1. Check the **PAYMENT_CART_LOGIC_GUIDE.md** for how things work
2. Check **FIXES_APPLIED.md** for what was changed
3. Compare your code with **newsales_FIXED.php**
4. Look at error messages in browser/terminal

---

**You got this! 💪 Take your time and follow each step carefully.**
