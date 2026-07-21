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
            'phone'=>'required'
            
        ];
        
        if(!$this->validate($rules)){
            return view('useraccountpage',[
            'validate'=>$this->validator]);
        }
        $firstname=trim($this->request->getpost('firstname',FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        $lastname=trim($this->request->getpost('lastname',FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $firstname = preg_replace('/\s+/', ' ', $firstname);
        $lastname  = preg_replace('/\s+/', ' ', $lastname);

        // Create a simple username from first+last (remove spaces)
        $username = strtolower(str_replace(' ', '', $firstname.$lastname));
        $phone = $this->request->getPost('phone');
        
        

        $rawPassword = $this->request->getPost('password');

        $password=password_hash($rawPassword,PASSWORD_DEFAULT);

        $email=$this->request->getpost('email',FILTER_SANITIZE_EMAIL);



        $model=new  UserModel();
        
        if($model->where('email',$email)->first()){

        return redirect()->back()
                         ->withInput()
                         ->with('message2',$email.'already exists');
        }
        //$user_id = 'USR'.random_int(100000000000,999999999999);


        $model->insert([
            
            
            'firstname'=>$firstname,
            'lastname'=>$lastname,
            
            'username'=>$username,
            'password'=>$password,
            'email'=>$email,
            'phone'=>$phone
        ]);
        
        return redirect()->to('userlogin')->with("success","user Account created");
        
        
    }
    
    public function todayssales(){
        
    }    


}



