<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customer';

    protected $primaryKey = 'customer_id';

    protected $allowedFields = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'address'
    ];

    public function searchCustomer($keyword)
    {
        return $this->like('first_name', $keyword)
                    ->orLike('last_name', $keyword)
                    ->findAll();
    }
}