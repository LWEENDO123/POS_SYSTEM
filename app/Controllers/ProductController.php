<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

class ProductController extends BaseController{

public function showProducts() {
    $this->trace('showProducts', 'ENTER | products page');
 
    $productModel = new ProductModel();
    $products = $productModel->getProductsWithCategory(50);

    $this->trace('showProducts', 'EXIT -> render Products | count={count}', ['count' => count($products)]);
    return view('Dashboard/Products', ['products' => $products]);
}

public function searchProducts(){
    $productModel = new ProductModel();

    $searchTerm = $this->request->getGet('search');


    $this->trace('searchProducts', 'ENTER | search={search}', ['search' => $searchTerm ?: '']);

   $productQuery = $productModel
                ->select('
                product.product_name,
                category.category_name,
                product.barcode,
                product.price,
                product.stock_quantity
                ')
                ->join(
                    'category',
                    'product.category_id=category.category_id'

                )
                ->orderBy('product.created_at','DESC')
                ->limit(50);

    if(!empty($searchTerm)){

        $productQuery->like('product.product_name', $searchTerm);
        log_message('debug','output found after condition: '.$searchTerm);
    }

    else{
        $this->trace('searchProducts', 'EXIT -> redirect productcontroller | no search term');
        return redirect()->to('productcontroller')->with('message','product not found');
    }
    $products = $productQuery->findAll();

    log_message('debug','products found(Ray debugging): '.count($products));
    log_message('debug','first product(Ray debugging): '.json_encode($products[0]??[]));

    $this->trace('searchProducts', 'EXIT -> render Products | count={count}', ['count' => count($products)]);
    return view('Dashboard/Products', ['products' => $products]);



} 
public function filterByCategory(){
    $categoryModel = new CategoryModel();
    $categoryName = $this->request->getGet('category');

    $this->trace('filterByCategory', 'ENTER | category={category}', ['category' => $categoryName ?: 'ALL']);

    $categoryQuery = $categoryModel
                ->select('
                product.product_name,
                category.category_name,
                product.price,
                product.stock_quantity,
                product.barcode
                

                ')
                ->join(
                    'product',
                    'product.category_id=category.category_id'
                )
                ->orderBy('product.created_at','DESC')
                ;
    if($categoryName !== 'ALL'){
        $categoryQuery->like('category.category_name', $categoryName);
    }

    $products = $categoryQuery->findAll();

    $this->trace('filterByCategory', 'EXIT -> render Products | category={category} count={count}', [
        'category' => $categoryName ?: 'ALL',
        'count'    => count($products),
    ]);
    return view('Dashboard/Products', ['products' => $products]);
}







}