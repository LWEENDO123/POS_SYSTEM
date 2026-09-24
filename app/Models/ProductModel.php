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

    public function products(){
        $product=new ProductModel();
        $products=$this->request->getGet('product_id');

        $product->where('product_id',$products)
                ->select('product_name,barcode,price,stock_quantity')
                ->limit(30)
                ->findAll();

    }
    public function productcategory(){
        $model=new CategoryModel();
        $category=$model->select('product_name,category_id,barcode,price,stock')
                          ->join('category',
                          'category.category_id=product.category_id')
                          ->orderBy('product_name','ASEC')
                          ->groupBy('product_name')
                          ->limit(30)
                          ->findAll();


    }
    
/*
    All database query-builder logic for products lives here in the MODEL.
    The controller only calls these methods - it never builds queries itself.



    Get products joined with their category name (used by Newsale()).
    */
    public function getProductsWithCategory($limit = 50)
    {
        return $this->select('
            product.product_id,
            product.product_name,
            product.barcode,
            product.price,
            product.stock_quantity,
            product.category_id,
            category.category_name
        ')
        ->join('category', 'category.category_id = product.category_id')
        ->orderBy('product.product_name', 'ASC')
        ->limit((int)$limit)
        ->findAll();
    }



    /*
    Filter products by a category name (used by display_products()).
    */
    public function getProductsByCategory($category, $limit = 50)
    {
        return $this->select('
            product.product_id,
            product.product_name,
            product.barcode,
            product.price,
            product.stock_quantity,
            product.category_id,
            category.category_name
        ')
        ->join('category', 'category.category_id = product.category_id')
        ->where('category.category_name',$category)
        ->orderBy('product.product_name', 'ASC')
        ->limit((int)$limit)
        ->findAll();
    }



    /*
    Search products by product name (partial match, used by search_product()).
    */
    public function searchProductsByName($search, $limit = 50)
    {
        return $this->select('
            product.product_id,
            product.product_name,
            product.price,
            product.barcode,
            product.stock_quantity,
            product.category_id,
            category.category_name
        ')
        ->join('category', 'category.category_id = product.category_id')
        ->like('product.product_name', $search)
        ->orderBy('product.product_name', 'ASC')
        ->limit((int)$limit)
        ->findAll();
    }



    /*
    Get ONE product joined with its category name (used by cart()).
    */
    public function getProductWithCategory($product_id)
    {
        return $this->select('
            product.product_id,
            product.product_name,
            product.price,
            product.stock_quantity,
            category.category_name
        ')
        ->join('category', 'category.category_id = product.category_id')
        ->where('product.product_id', $product_id)
        ->first();
    }

    /*
    Reduce a product's stock by $qty, but only if enough stock exists.
    Returns true on success, false if stock is insufficient.
    (Used by payment_success - OPTION B stock reduction.)
    */
    public function decrementStock($product_id, $qty)
    {
        return $this->where('product_id', $product_id)
                    ->where('stock_quantity >=', $qty)
                    ->set('stock_quantity', 'stock_quantity - ' . (int)$qty, false)
                    ->update();
    }
}