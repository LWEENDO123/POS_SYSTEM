<?php

namespace App\Controllers;
use App\Models\SaleModel;
use App\Models\PaymentTransaction;

class Cron extends BaseController{

    /**
     * Aliases: markbypending() / markByPending()
     * Convenience name for the cron job that marks stale PENDING
     * payments/sales as FAILED after the timeout.
     */
    public function markbypending()
    {
        $this->trace('markbypending', 'ENTER | alias -> checkPaymentTransactionPending');
        return $this->checkPaymentTransactionPending();
    }

    public function checkPaymentTransactionPending(){

    $this->trace('checkPaymentTransactionPending', 'ENTER | cron job for stale PENDING payments');

    $sale_model = new SaleModel();
    $payment_transaction_model = new PaymentTransaction();
    
    // Find the MOST RECENT pending transaction (by created_at desc)
    $recent_pending = $payment_transaction_model
        ->where('transaction_status', 'PENDING')
        ->orderBy('created_at', 'DESC')
        ->first();

    $this->trace('checkPaymentTransactionPending', 'LOOKUP | most recent PENDING transaction found={found}', [
        'found' => $recent_pending ? ('txn #' . $recent_pending['transaction_id']) : 'no',
    ]);
    
    if(!$recent_pending){
        $this->trace('checkPaymentTransactionPending', 'EXIT | nothing to do');
        return redirect()->back()->with('message', 'No pending transactions to check');
    }

    $timeout_seconds = 40; // Configurable timeout
    $created_at = strtotime($recent_pending['created_at']);
    $current_time = time();
    $diff = $current_time - $created_at;

    $this->trace('checkPaymentTransactionPending', 'EVALUATE | txn={txn_id} age={age}s timeout={timeout}s', [
        'txn_id'  => $recent_pending['transaction_id'],
        'sale_id' => $recent_pending['sale_id'],
        'age'     => (int) $diff,
        'timeout' => $timeout_seconds,
    ]);
    
    if($diff > $timeout_seconds){
        // Update both sale and transaction status to FAILED
        $sale_model->update($recent_pending['sale_id'], ['status' => 'FAILED']);
        $payment_transaction_model->update($recent_pending['transaction_id'], [
            'transaction_status' => 'FAILED'
        ]);

        $this->trace('checkPaymentTransactionPending', 'MARKED FAILED | sale_id={sale_id} transaction_id={txn_id}', [
            'sale_id' => $recent_pending['sale_id'],
            'txn_id'  => $recent_pending['transaction_id'],
        ]);
        
        return redirect()->back()->with('message', 'Transaction timed out and marked as FAILED');
    }

    $this->trace('checkPaymentTransactionPending', 'STILL PENDING | within grace period, not marked yet');
    return redirect()->back()->with('message', 'Transaction still pending, not yet timed out');
    }
}