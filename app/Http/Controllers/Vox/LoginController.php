<?php

namespace App\Http\Controllers\Vox;

use Redirect;
use Request;
use Session;
use View;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class LoginController extends Controller
{
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
      if (Session::get('user_id') != '') {
          return redirect::to('/dashboard');
      }
      
        $vox = $this->mandatory();
        $vox['page_type'] = 'admin_login';
        return view('vox.login')->with($vox);
    }
    
    public function signup(Request $request)
    { 
      try{
        $params = $request::all();
        $endpoint = "https://api-sport-events.php6-02.test.voxteneo.com/api/v1/users";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL,$endpoint);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params)); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        $result=curl_exec($ch);
        curl_close($ch);
        $content = json_decode($result, true);
        if(isset($content['errors'])){
          return ['error' => 1, 'message'=> $content['message'], 'data' => $content['errors']];
        }
        else{
          return ['error' => 0, 'message'=> 'Registering success, You can login now', 'data' => $content];
        }
      } catch (\Exception $ex) {
        return ['error' => 1, 'message' => $ex->getMessage().' File: '.$ex->getFile()." Line:".$ex->getLine()];
      }
    }

    public function checkSession() {
    $session = Session::get(null);
    
    $arr = [
        'auth' => isset($session['user']) ? $session['user'] : '',
    ];
                
    return json_encode($arr);
  }
  
    public function logout(){
      Session::flush();
      return redirect::to('/');
    }
    
    public function changePassword(){
      if (Session::get('user_id') == '') {
          return redirect::to('/');
      }
      
        $vox = $this->mandatory();
        $vox['page_type'] = 'change_password';
        return view('vox.change-password')->with($vox);
    }
    
    public function updatePassword(Request  $request){
      if (Session::get('user_id') == '') {
          return redirect::to('/');
      }
      
      $params = $request::all();
      if($params['repeatPassword'] != $params['newPassword']){
        return redirect('change-password')->with('message','Password confirmation is not same with new password');
      }
//      dd($params);
      $endpoint = "https://api-sport-events.php6-02.test.voxteneo.com/api/v1/users/".Session::get('user_id')."/password";
      $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params));
        curl_setopt($ch, CURLOPT_URL,$endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '. Session::get('token'),
        ]);
        $result=curl_exec($ch);
        
        curl_close($ch);
        $content = json_decode($result, true);

        if(isset($content['message']) && isset($content['status_code'])){
          return redirect('change-password')->with('message',$content['message']);
        }
        return redirect('change-password')->with('message','Password have been updated');
        
    }

    
    public function activate($email) {
      $customer = Customer::where('email', $email)->firstOrFail();
      $customer->is_active = 1;
      $customer->save();

      $session = Session::get(null);
      $page_type = 'activation';

      return view('frontend.activate', compact('customer', 'session', 'page_type'));
    }
    
    
    public function loginSubmit(Request $request){
      try{
        $params = $request::all();
        $endpoint = "https://api-sport-events.php6-02.test.voxteneo.com/api/v1/users/login";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL,$endpoint);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($params)); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json'
        ]);
        $result=curl_exec($ch);
        curl_close($ch);
        $content = json_decode($result, true);
        if(isset($content['id'])){
          Session::put('user_id',$content['id']);
          Session::put('email',$content['email']);
          Session::put('token',$content['token']);
          return ['error' => 0, 'message'=> '', 'data' => $content];
        }
        else{
          return ['error' => 1, 'message'=> $content['message'], 'data' => $content['errors']];
        }
      } catch (\Exception $ex) {
        return ['error' => 1, 'message' => $ex->getMessage().' File: '.$ex->getFile()." Line:".$ex->getLine()];
      }
    }
}
