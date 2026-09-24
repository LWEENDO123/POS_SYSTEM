<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentTransaction extends Model
{
    protected $table = 'paymenttransactions';

    protected $primaryKey = 'transaction_id';

    protected $allowedFields = [
        'sale_id',
        'payment_method',
        'transaction_status',
        'gateway_reference',
        'amount_attempted',
        'created_at',

    ];
/*
    Get the latest payment transaction for a sale (used by payment_success()).
    */
    public function getTransactionBySale($sale_id)
    {
        return $this->select('payment_method, transaction_status')
                    ->where('sale_id', $sale_id)
                    ->orderBy('transaction_id', 'DESC')
                    ->first();
    }

    /*
    Update the transaction status for a sale (used by payment_failed,
    payment_pending, payment_cancelled). Only works when there is ONE
    transaction row per sale (the normal POS flow).
    */
    public function markStatusBySale($sale_id, $status)
   {
        return $this->where('sale_id', $sale_id)
                    ->set('transaction_status', $status)
                    ->update();
    }

    /*
    Find a transaction by its gateway reference (used by process_payment to
    prevent duplicate processing).
    */
    public function getByGatewayReference($gateway_reference)
    {
        return $this->where('gateway_reference', $gateway_reference)->first();
    }

    // ============================================================
    // IDEMPOTENCY PROTECTION - Model Level Methods
    // ============================================================
    // 
    // PROBLEM: Cashier double-clicks "Pay" or network retries send
    //          the same gateway_reference twice
    // SOLUTION: These methods provide atomic check-then-insert logic
    // WHY: Database unique index is the ultimate guard, but these
    //      methods handle the PHP-side logic gracefully
    // ============================================================

    /**
     * Find existing transaction by gateway_reference (idempotency lookup).
     * 
     * IDEMPOTENCY: Call this BEFORE processing payment to detect duplicates.
     * 
     * @param string $gateway_reference Unique ID from payment gateway
     * @return array|null Transaction data or null if not found
     */
    public function findByGatewayReference(string $gateway_reference): ?array
    {
        return $this->where('gateway_reference', $gateway_reference)->first();
    }

    /**
     * Atomically create transaction ONLY if gateway_reference is new.
     * 
     * IDEMPOTENCY: Returns [transaction_id, is_new] where is_new=false
     *              means duplicate detected - existing transaction returned.
     * 
     * @param array $data Transaction data (must include gateway_reference)
     * @return array [int transaction_id, bool is_new]
     * @throws \RuntimeException if insert fails for non-duplicate reason
     */
    public function createIdempotent(array $data): array
    {
        // Step 1: Check if already exists (fast lookup)
        $existing = $this->findByGatewayReference($data['gateway_reference']);
        if ($existing) {
            // Duplicate detected - return existing, mark as not new
            return [$existing['transaction_id'], false];
        }
        
        // Step 2: Try to insert new transaction
        $transaction_id = $this->insert($data);
        if ($transaction_id !== false) {
            // Success - new transaction created
            return [$transaction_id, true];
        }
        
        // Step 3: Insert failed - could be race condition (another request
        //         inserted same gateway_reference between our check and insert)
        //         Check one more time
        $existing = $this->findByGatewayReference($data['gateway_reference']);
        if ($existing) {
            return [$existing['transaction_id'], false];
        }
        
        // Step 4: Genuine error (validation, DB constraint, etc.)
        throw new \RuntimeException('Failed to create payment transaction: ' . implode(', ', $this->errors()));
    }
}
