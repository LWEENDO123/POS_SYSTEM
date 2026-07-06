<?php
//app/Controllers/CreateAccountController.php
namespace App\Controllers;
use App\Models\UserModel;
use CodeIgniter\Model;


class CreateAccount extends BaseController{


    public function user_page():string{
        return view('useraccountpage');
    
       
    }

    public function user_registration(){
        $rules=[
            'firstname'=>'required',
            'lastname'=>'required',
            'password'=>'required',
            'email'=>'required|valid_email',
            
        ];
        
        if(!$this->validate($rules)){
            return view('useraccountpage',[
            'validate'=>$this->validator]);
        }
        $firstname=$this->request->getpost('firstname',FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $lastname=$this->request->getpost('lastname',FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $username=strtolower("{$firstname}_{$lastname}");
        $rawPassword = $this->request->getPost('password');
        $password=password_hash($rawPassword,PASSWORD_DEFAULT);
        $email=$this->request->getpost('email',FILTER_SANITIZE_EMAIL);

        $model=new  UserModel();
        
        if($model->where('username',$username)->first()){
            return redirect()->back()
                             ->withInput()
                             ->with('message1',"{$username}  already exists");


        }

        if($model->where('email',$email)->first()){

        return redirect()->back()
                         ->withInput()
                         ->with('message2',$email.'already exists');
        }

        $model->insert([
            'firstname'=>$firstname,
            'lastname'=>$lastname,
            'username'=>$username,
            'password'=>$password,
            'email'=>$email
        ]);
        
        return redirect()->to('userlogin')->with("success","user Account created");
        
          

           
        
      
        
        
    }


}
    
    

    



    




