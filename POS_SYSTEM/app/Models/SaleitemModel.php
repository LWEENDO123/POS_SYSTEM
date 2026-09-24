<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleItemModel extends Model
{
    protected $table = 'sale_item';

    protected $primaryKey = 'sale_item_id';

    protected $allowedFields = [
        'sale_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'Date',
        'created_by_user'

    ];
/*
    Get all sale items belonging to a sale (used by checkout validation,
    payment_success stock reduction, and receipt display).
    */
    public function getItemsBySale($sale_id)
    {
        return $this->where('sale_id', $sale_id)->findAll();
    }

    /*
    Get a sale's items WITH product names (used by Receipt::view_receipt).
    The product JOIN lives here in the MODEL - the controller only calls
    this method (MVC rule: queries in models, views in controllers).
    */
    public function getItemsBySaleWithProducts($sale_id)
    {
        return $this->select('sale_item.*, product.product_name, product.price')
                    ->join('product', 'product.product_id = sale_item.product_id', 'left')
                    ->where('sale_item.sale_id', $sale_id)
                    ->findAll();
    }
}
