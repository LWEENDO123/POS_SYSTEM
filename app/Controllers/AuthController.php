<?php
//app/AuthController.php
namespace App\Controllers;

use  CodeIgniter\Model;
use  App\Models\UserModel;

class AuthController extends BaseController{
    public function loginpage():string{
    $this->trace('loginpage', 'ENTER | render login form');
    return view('auth/login');
    }

    public function userlogin(){

        $this->trace('userlogin', 'ENTER | login attempt');

        $username=trim($this->request->getPost('username'));
        $username=preg_replace('/\s+/','',$username);
        $password=$this->request->getPost('password');

        $rules=[
            'username'=>'required',
            'password'=>'required',

        ];

        if(!$this->validate($rules)){
        $this->trace('userlogin', 'VALIDATION FAILED | username/password required');
        return view('auth/login',[
            'validate'=>$this->validator]);
        }

        if($username){
        $model=new UserModel();

        $user = $model->where('username', $username)->first();

        $this->trace('userlogin', 'USER LOOKUP | username={username} found={found}', [
            'username' => $username,
            'found'    => $user !== null ? 'yes' : 'no',
        ]);

        
        if ($user && password_verify($password, $user['password'])) {
            $this->trace('userlogin', 'CREDENTIALS OK | password verified');
            session()->set([
                'user_id'=>$user['user_id'],
                'username'=>$user['username'],
                'role'=>$user['role'],
                'logged_in'=>true

            ]
           

            );


            $role=session()->get('role');

            $this->trace('userlogin', 'SESSION ESTABLISHED | user={user} role={role}', [
                'user' => $user['username'],
                'role' => $role,
            ]);

            if($role=='cashier'){
            $this->trace('userlogin', 'EXIT -> redirect DashBoard/index');
            return redirect()->to('DashBoard/index')->with('success', "Welcome ".$user['username']);
            }
            
        } 
        else {
            $this->trace('userlogin', 'CREDENTIALS INVALID | password mismatch or user missing');
            return redirect()->to('userlogin')->with('message', 'Invalid credentials');}






        
        }
    }

    public function logout(){

        $this->trace('logout', 'ENTER | user logging out');
        session()->destroy();
        $this->trace('logout', 'SESSION DESTROYED | redirect to login');
        return redirect()->to('userlogin')->with('success', 'logout successfull');

    }

}