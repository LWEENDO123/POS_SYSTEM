<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    protected $table = 'product';

    protected $primaryKey = 'product_id';

    protected $allowedFields = [
        'product_name',
        'barcode',
        'price',
        'stock_quantity',
        'category_id',
        'created_by_user_id'
    ];

    public function getLowStockProducts()
    {
        return $this->where('stock_quantity <', 10)
                    ->findAll();
    }

    public function searchProducts($keyword)
    {
        return $this->like('name', $keyword)
                    ->findAll();
    }
}