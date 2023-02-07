<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Session;
use Socialite;
use App\Models\Customer;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';
    protected $logoutRedirectTo = '/';
    protected $guard = 'customers';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function showLoginForm()
    {
        return view('frontend.auth.login');
    }

    public function login(Request $request)
    {
        // Validate the form data
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:6'
        ]);

        // Attempt to log the user in
        if (Auth::guard('customers')->attempt(['email' => $request->email, 'password' => $request->password])) {
          
          $customer = Customer::where('email','=',$request->email)
              ->get();
          
          $auth = [
            'id' => $customer[0]->id,
            'email' => $customer[0]->email,
            'name' => $customer[0]->name,
          ];
          
          Session::put('user',$auth);
          
            // if successful, then redirect to their intended location
            return redirect()->route('frontend.dashboard.student');
        }

        // if unsuccessful, then redirect back to the login with the form data
        return redirect()
                ->back()
                ->withErrors(['Failed to login. Please check your credentials and try again.'])
                ->withInput($request->only('email'));
    }

    /**
     * Log the user out of the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request)
    {
        Session::forget('user');
        $cart = Session::get('cart');
        unset($cart);
        $cart = [];
        Session::put('cart', $cart);
        Auth::guard('customers')->logout();
        return redirect($this->logoutRedirectTo);
    }

    /**
     * Redirect the user to the google authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Obtain the user information from google.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback($provider)
    {
        if($provider == 'facebook'){
            if(isset($_GET['error_code'])){
                echo "login failed..";
                echo "<script>window.setTimeout(function(){window.location.href = '/'; }, 1000);</script>";
                // return redirect()->route('front.home');
            }
            
        }
        $user = Socialite::driver($provider)->user();
        $customer = Customer::where('email','=',$user->email)
          ->first();

        // if user have account
        if (isset($customer)) {
          
          // Auth::guard('customers')->attempt(['email' => $user->email, 'password' => $customer->password]);
          
          $auth = [
            'id' => $customer->id,
            'email' => $customer->email,
            'name' => $customer->name,
          ];

          Session::put('user',$auth);

          Auth::guard('customers')->login($customer);
          
          return redirect()->route('front.home');
        } else {
          // if user doesn't have account
          $auth = [
            'email' => $user->email,
            'name' => $user->name,
          ];

          Session::put('register', $auth);
          return redirect()->route('frontend.register');
        }

        // if unsuccessful, then redirect back to the login with the form data

        // return $user->token;
    }
}
