<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table = 'sale';

    protected $primaryKey = 'sale_id';

protected $allowedFields = [
        
        'sale_number',
        'status',
        'total_amount',
        'sale_date',
        'handled_by_user'

    ];

    

public function getSales($limit = NULL){ 
        

    
$this->select("
    sale.sale_id, 
    sale.sale_date,
    sale.total_amount,
    sale.status,
    user.username AS username,
    SUM(sale_item.quantity) AS items_count
")
->join('user', 'user.username = sale.handled_by_user', 'left')
->join('sale_item', 'sale_item.sale_id = sale.sale_id', 'left')
->groupBy('sale.sale_id')
->orderBy('sale.sale_date', 'DESC');

            if(
                empty($limit)
            ){
                $limit=3;
                $this->limit($limit);

            }
            log_message('debug', 'First sale row: ' . json_encode($results[0] ?? []));
            return $this->findAll();

    }

    public function getSaleStatus($sale_id)
    {
        return $this->select('status')->find($sale_id);
    }

    /*
    Mark a sale's status (used by payment_success/failed/pending/cancelled).
    */
    public function updateStatus($sale_id, $status)
    {
        return $this->update($sale_id, ['status' => $status]);
    }

    
    }

