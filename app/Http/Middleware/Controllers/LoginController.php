<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Redirect;
use App\Models\Customer;
use Carbon\Carbon;
use DB;
use Validator;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use App\Http\Controllers\API;

class LoginController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
      
    }
    
    public function signup(Request $request)
    { 
      $params = $request->all();
      
      $customer = Customer::where('email','=',$params['email'])
              ->get();
      
      $arr = [
            'error' => 1,
            'message' => 'failed',          
        ];
      
      if(count($customer)>0){
        
        $arr = [
            'error' => 1,
            'message' => 'email already used',          
        ];
        
      }else{
        
        $validator = Validator::make($request->all(), [
          'name' => 'required',
          'email' => 'required|unique:customers',
          'password' => 'required'
        ]);

        if ($validator->fails()) {
            return json_encode($arr);
        }

        try {
          $customer = new Customer;
          $customer->name = $request->name;
          $customer->email = $request->email;
          $customer->password = empty($request->password) ? bcrypt('admin') : bcrypt($request->password);
          $customer->updated_at = date('Y-m-d H:i:s');
          $customer->save();

          $arr = [
            'error' => 0,
            'message' => 'account have been create',          
          ];
        
          // Send Email
          $email['template'] = 'emails.register';
          $email['from_email'] = 'lenterabagibangsa@funedge.co.id';
          $email['email'] = $request->email;
          $email['name'] = $request->name;
          $email['link'] = route('front.activate', ['email' => $request->email]);
          $email['subject'] = 'Registration Success';
          API::sendEmail($email);

        } catch (\Exception $e) {
          $arr['message'] = $e->getMessage();
          return json_encode($arr);
        }
        
        
      }
      
      return json_encode($arr);
    }
    
    public function store(Request $request){
      $params = $request->all();
      
      $arr = [
          'error' => 1,
          'message' => 'Failed to login. Please check your credentials and try again.',          
      ];
      
      
      $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        // Attempt to log the user in
        if (Auth::guard('customers')->attempt(['email' => $request->email, 'password' => $request->password])) {
            // if successful, then redirect to their intended location
          
          $customer = Customer::where('email','=',$params['email'])
              ->get();
          
          $auth = [
            'id' => $customer[0]->id,
            'email' => $customer[0]->email,
            'name' => $customer[0]->name,
          ];
          
          Session::put('user',$auth);
            
          $arr = [
            'error' => 0,  
            'message' => '', 
            'auth' => $auth, 
          ];
        }
        
      return json_encode($arr);
    }
    
    public function checkSession() {
    $session = Session::get(null);
    
    $arr = [
        'auth' => isset($session['user']) ? $session['user'] : '',
    ];
                
    return json_encode($arr);
  }
  
    public function logout(){
      Session::forget('user');
    }
    
    public function logoutGet(){
      Session::forget('user');
    }

    public function activate($email) {
      $customer = Customer::where('email', $email)->firstOrFail();
      $customer->is_active = 1;
      $customer->save();

      $session = Session::get(null);
      $page_type = 'activation';

      return view('frontend.activate', compact('customer', 'session', 'page_type'));
    }
    
}
