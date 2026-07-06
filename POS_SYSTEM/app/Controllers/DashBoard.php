<?php
//App/controllers/DashBoard

namespace App\Controllers;

use App\Models\ProductModel;

class DashBoard extends BaseController{

    public function index(){
    if(!session()->get('logged_in')){

        return redirect()->to('userlogin')->with('message','session expired or invalid');
    }
    $user=session()->get('username');
    $model=new ProductModel();

    $showproducts=$model->orderBy('created_by_user_id','ASEC')
                        ->limit(5)
                        ->findAll();

    $names = [];
    foreach($showproducts as $product){
        $names[]=[ 
        'name'=>$product['name'],
        'barcode'=>$product['barcode'],
        'price'=>$product['price'],
        'stock_quantity'=>$product['stock_quantity'],
        'category_id'=>$product['category_id'],
        ];
        
    }

    return view('DashBoard/index', [
        'user'=>$user,
        'products'=>$showproducts,
        'names'=>$names
        
        ]);

     
    


    }

    public function totalproducts(){


        $model=new ProductModel();
        $totalproducts=$model->selectSum('stock_quantity')->get()->getRow()->stock_quantity;
       return view('Dashboard/index',[
        'totalproducts'=>$totalproducts,
       ]);

    }



  
}

