<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use App\Models\Student;
use App\Http\Controllers\API;

class PageController extends Controller
{
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function about()
    {
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'about';
      return view('frontend.about')->with($lbb);
    }
    
    public function search()
    {
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'seacrh';
      $lbb['school_param'] = Student::where('is_sponsored','0')->groupby('slh_location')->select('slh_location')->get();
      $result = Student::where('is_sponsored','0')->orderBy('birth_date','desc')->select('birth_date')->limit(1)->get();
      
      if(count($result)>0){
        $min_age = $result[0]->birth_date;
        $lbb['min_age'] = date("Y") - substr($min_age,0,4);
      }else{
        $lbb['min_age'] = 0;
      }
      
      $result = Student::where('is_sponsored','0')->orderBy('birth_date','asc')->select('birth_date')->limit(1)->get();
      if(count($result)>0){
        $max_age = $result[0]->birth_date;
        $lbb['max_age'] = date("Y") - substr($max_age,0,4);
      }else{
        $lbb['max_age'] = 0;
      }
      
      return view('frontend.search')->with($lbb);
    }
    
    public function faq()
    {
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'faq';
      return view('frontend.faq')->with($lbb);
    }
    
    public function students(){
      $lbb['session'] = Session::get(null);
      $lbb['students'] = Student::where('is_sponsored','0')->get();
      
    }
    
    public function session(){
      dd(Session::get(null));
    }
    
    public function policies()
    {
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'policies';
      return view('frontend.policies')->with($lbb);
    }
    
    public function tnc()
    {
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'tnc';
      return view('frontend.tnc')->with($lbb);
    }
    
    public function protectionPolicy()
    {
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'protection_policy';
      return view('frontend.protection_policy')->with($lbb);
    }
    
    public function thankyou()
    {
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      
      if(!isset($_GET['debug'])){
        if(!isset($lbb['session']['transaction'])){
          return redirect()->route('front.home');
        }else{
          $lbb['session']['cart'] = [];
          Session::put('cart',array());
          Session::forget('transaction');
        }
      }
      
      $lbb['page_type'] = 'thankyou';
      return view('frontend.thankyou')->with($lbb);
    }
    
    public function failed()
    {
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'failed';
      return view('frontend.failed')->with($lbb);
    }
    
}
