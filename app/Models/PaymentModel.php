<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payment';

    protected $primaryKey = 'payment_id';

    protected $allowedFields = [
        'sale_id',
        'amount',
        'payment_method',
        'payment_status',
        'processed_by_user_id',
        'payment_date'
    ];

    /*
    Get the payment record for a sale (used by Newsales::receipt).
    */
    public function getPaymentBySale($sale_id)
    {
        return $this->select('payment_method, payment_status')
                    ->where('sale_id', $sale_id)
                    ->first();
    }
}
