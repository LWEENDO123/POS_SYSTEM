<?php
//app/AuthController.php
namespace App\Controllers;

use  CodeIgniter\Model;
use  App\Models\UserModel;

class AuthController extends BaseController{
    public function loginpage():string{
    return view('auth/login');
    }

    public function userlogin(){

        $username=trim($this->request->getPost('username'));
        $username=preg_replace('/\s+/','',$username);
        $password=$this->request->getPost('password');

        $rules=[
            'username'=>'required',
            'password'=>'required',

        ];

        if(!$this->validate($rules)){
        return view('auth/login',[
            'validate'=>$this->validator]);
        }

        if($username){
        $model=new UserModel();

        $user = $model->where('username', $username)->first();

        
        if ($user && password_verify($password, $user['password'])) {
            session()->set([
                'user_id'=>$user['user_id'],
                'username'=>$user['username'],
                'role'=>$user['role'],
                'logged_in'=>true

            ]
           

            );


            $role=session()->get('role');

            if($role=='cashier'){
            return redirect()->to('DashBoard/index')->with('success', "Welcome ".$user['username']);
            }
            //return view('dashboard/index')
            //return ;
        } 
        else {
            return redirect()->to('userlogin')->with('message', 'Invalid credentials');}






        
        }
    }
    public function logout(){

        session()->destroy();
        return redirect()->to('userlogin')->with('message', 'logout successfully');

    }

}