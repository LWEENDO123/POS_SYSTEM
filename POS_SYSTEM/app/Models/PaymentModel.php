<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payment';

    protected $primaryKey = 'payment_id';

    protected $allowedFields = [
        'sale_id',
        'payment_method',
        'amount_paid',
        'payment_date'
    ];
}