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

    public function getSales($limit = NULL,$status ='PAID'){ 
        

    
     $this->select("
                sale.sale_id, 
                sale.sale_date,
                sale.total_amount,
                sale.status,
                customer.firstname,
                user.username,
                COUNT(sale_item.sale_item_id) AS items_count
            ");

            $this->join('customer', 'customer.customer_id = sale.customer_id', 'left');
            $this->join('user', 'user.user_id = sale.user_id', 'left');
            $this->join('sale_item', 'sale_item.sale_id = sale.sale_id', 'left');
            $this->where('status',$status);
            $this->groupBy('sale.sale_id');
            $this->orderBy('sale.sale_date', 'DESC');
            if(
                empty($limit)
            ){
                $limit=3;
                $this->limit($limit);

            }
            return $this->findAll();

    }
}
