<?php

namespace App\Models;

use CodeIgniter\Model;

class ReceiptModel extends Model
{
    protected $table = 'receipt';

    protected $primaryKey = 'receipt_id';

    protected $allowedFields = [
        'sale_id',
        'receipt_number',
        'generated_at',
        'handeled_by_user',
        'total_amount'
    ];

    /*
    Get all receipts, newest first (used by Receipt::index and Newsales::receipts).
    */
    public function getAllReceipts($limit = 50)
    {
        return $this->orderBy('generated_at', 'DESC')->limit((int)$limit)->findAll();
    }

    /*
    Search receipts by receipt number (partial match, used by Receipt::search).
    */
    public function searchByNumber($search, $limit = 50)
    {
        return $this->like('receipt_number', $search)
                    ->orderBy('receipt_id', 'DESC')
                    ->limit((int)$limit)
                    ->findAll();
    }

    /*
    Find a receipt by its sale_id (used by Receipt::view_receipt).
    */
    public function getBySale($sale_id)
    {
        return $this->where('sale_id', $sale_id)->first();
    }

    /*
    NOTE: view_receipt() was REMOVED from this model on purpose.
    A MODEL is data-only: returning view() or using $this->response from a
    model breaks MVC and crashes ($this->response does not exist on models).
    The receipt preview page is now rendered by Receipt::view_receipt()
    (the CONTROLLER), which calls the query methods above.
    */
}