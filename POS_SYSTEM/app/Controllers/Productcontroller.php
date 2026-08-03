<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Model;

Class Productcontroller extends BaseController{

public function Products() {
    return view('products/product');

}
public function search_product(){
    $model=new ProductModel();
    $search=$this->request->getGet('search');

   $get_products=$model
                ->select('
                product.product_name,
                category.category_name,
                product.barcode,
                sale.price,
                product.stock_quantity
                ')
                ->join(
                    'category',
                    'product.category_id=category.category_id'

                )
                ->groupBy('product_name')
                ->orderBy('created_at','DESC')
                ->limit(50);

    if($search){
        $get_products->like('product_name',$search);
    }
    if(empty($get_products)){
        return redirect()->to('product/products')->with('message','product not found');
    }
    return view('product/products',['products'=>$get_products]);



} 
public function categories_navbar(){
    $category_model=new CategoryModel();
    $category_name=$this->request->getGet('category');

    $category_query=$category_model
                ->select('
                product.product_name,
                category.category_name,
                product.price,
                product.stock

                ')
                ->join(
                    'product',
                    'product.category_id=category.category_id'
                )
                
                ->groupBy('product_name')
                ->orderBy('created_at','DESC')
                ->limit(50);

    if($category_name !== 'ALL'){
        $category_query->where('category_name',$category_name);
    }

    $results = $category_query->findAll();

    return view('product/products', ['products' => $results]);
}







}