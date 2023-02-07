<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Redirect;
use App\Http\Controllers\API;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
//    public function __construct()
//    {
//        $this->middleware('auth');
//    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'home';
      return view('frontend.home')->with($lbb);
    }
    
    public function setLanguage($lang){
      if($lang !== ''){
        Session::put('language',strtolower($lang));
      }
      
      return Redirect::back();
    }
}
