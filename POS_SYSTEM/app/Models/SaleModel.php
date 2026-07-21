<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sale';

    protected $primaryKey = 'sale_id';

    protected $allowedFields = [
        
        'customer_id',
        'user_id',
        'sale_date',
        'total_amount',
        'status'
    ];
}