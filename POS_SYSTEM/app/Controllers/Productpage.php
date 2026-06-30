<?php

namespace App\Controllers;
use App\Models\ProductModel;
use  CodeIgniter\Model;
use Config\View;

class Productpage extends BaseController{

    public function index():string{
        return view('index');

    }

    public function allproducts(){
        $model=new ProductModel();
        $Allproduct=$model->findAll();

        return view('Allproducts',[ // fixed: view name now matches the actual file app/Views/Allproducts.php
            'allproducts'=>$Allproduct // fixed: this variable name must match $allproducts in the view
        ]);

    }
    public function oneproduct(){
        $product_id=$this->request->getGET('product_id');
        $oneproduct=null; // fixed: keeps the view working before a product_id is submitted
        if(isset($product_id)){
        $model=new ProductModel();
        $oneproduct=$model->find($product_id);
        }

        return view('oneproduct',[
            'oneproduct'=>$oneproduct // fixed: this variable name must match $oneproduct in the view
        ]);
    }

    public function search($product_name=null){
        $product_name=$product_name ?? $this->request->getGet('name'); // fixed: supports /products/search/apple and ?name=apple
        $model=new ProductModel();

        if(isset($product_name)){
            $search=$model->where('name',$product_name)->findAll();

            

            

            return view('index',[
                'search'=>$search
            ]);
        }

        return view('index'); // fixed: return a view even when no search value is provided
    }

    public function lowstock(){
        $model=new ProductModel();

        $lowquantity = $model->where('stock_quantity <', 20)->findAll(); // fixed: low stock means less than 20
        return view('index',[
            'lowquantity'=>$lowquantity
        ]);
    }

}
