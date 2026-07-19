<?php

namespace App\Controllers;
use App\Models\ProductModel;
use CodeIgniter\Model;

Class Productcontroller extends BaseController{

    public function search(){
$usersearch = $this->request->getGet('search');
        if (!is_string($usersearch)) {
            $usersearch = '';
        }


        $model=new ProductModel();

        $search=$model->like('name',$usersearch)
                      
                      ->findAll();

        return  view('DashBoard/index',[
            'searchresult'=>$search
        ]);             
    }





}