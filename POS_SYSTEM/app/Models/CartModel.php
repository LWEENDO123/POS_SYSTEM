<?php
namespace App\Models;
use  CodeIgniter\Model;

class CartModel extends Model{
    protected $table='cart';
    protected $primaryKey = 'cart_id';
    protected $allowedFields = [
        'product_id',
        'product_name',
        'category_id',
        'price'
    ];
    
}




?>