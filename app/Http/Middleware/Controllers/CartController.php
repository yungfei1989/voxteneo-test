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

class CartController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
      
    }
    
    public function store(Request $request)
    { 
      $cart = Session::get('cart');
      
      $arr = [
          'total' => count($cart),
          'data' => $cart,
      ];
      
      return json_encode($arr);
    }
    
    public function removeCart(Request $request){
      $params = $request->all();
        
      $cart = Session::get('cart');
      $removed_cart = Session::get('removed_cart');
      $removed_cart[] = $cart[$params['item_row']][$params['item_line_row']];
      Session::put('removed_cart', $removed_cart);
      
      unset($cart[$params['item_row']][$params['item_line_row']]);
      
      if(count($cart[$params['item_row']]) == 0){
        unset($cart[$params['item_row']]);
      }
      
      Session::put('cart',$cart);
      
      $arr = [
          'total' => count($cart),
          'data' => $cart,
      ];
      
      return json_encode($arr);
    }
    
    public function addCart(Request $request)
    {
        $params = $request->all();
        
        $cart = Session::get('cart');
        
        if(isset($cart[$params['id']]) && count($cart[$params['id']])>0){
          foreach($cart[$params['id']] as $cart_content){
            if($cart_content['item_type'] == 'donation'){
              //donation
              if($cart_content['student']['id'] !== $params['student']['id']){
                $cart[$params['id']][] = $params;
              }
            }else{
              //gift
              $cart[$params['id']][] = $params;
            }
          }          
        }else{
          $cart[$params['id']][] = $params;
        }
        
        Session::put('cart',$cart);
        return json_encode($params);
    }
    
}
