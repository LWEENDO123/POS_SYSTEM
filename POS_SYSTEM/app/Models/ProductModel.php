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
    public function deletecategory($category_id){
       

       if( !$this->where('category_id',$category_id)
                                 
        ->limit(1)
        ->delete()
         
        ){
            return false;

        }


       
    }
}