<?php

namespace App\Models;

use CodeIgniter\Model;

class CustomerModel extends Model
{
    protected $table = 'customer';

    protected $primaryKey = 'customer_id';

    protected $allowedFields = [
        'firstname',
        'phone',
        'email',
        
    ];

    public function searchCustomer($keyword)
    {
        return $this->like('first_name', $keyword)
                    ->orLike('last_name', $keyword)
                    ->findAll();
    }
}