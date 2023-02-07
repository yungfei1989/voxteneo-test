<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Session;
use Redirect;
use App\Models\Student;
use App\Models\Item;
use App\Models\PriceList;
use App\Http\Controllers\API;
use App\Models\PriceListLine;

class ItemController extends Controller
{
    public function donation()
    { 
      $location = isset($_GET['slh_locations'])?$_GET['slh_locations']:'';
      $academy = isset($_GET['grade'])?$_GET['grade']:'';
      $gender = isset($_GET['gender'])?$_GET['gender']:'';
      $age1 = isset($_GET['age1'])?$_GET['age1']:'';
      $age2 = isset($_GET['age2'])?$_GET['age2']:'';
      $year1 = isset($_GET['year1'])?$_GET['year1']:'';
      $year2 = isset($_GET['year2'])?$_GET['year2']:'';
      $name = isset($_GET['name'])?$_GET['name']:'';
      
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'donation';
      $sql = Student::where('is_sponsored','=','0');
      
      
      if($name !== ''){
        $sql->where('first_name','like','%'.$name.'%');
      }
      if($gender !== ''){
        $sql->whereIn('gender',$gender);
      }
      if($academy !== '' && !in_array('all',$academy)){
        $sql->whereIn('academy', $academy);
      }
      if($location !== '' && !in_array('all',$location) ){
        $sql->whereIn('slh_location', $location);
      }
      
      $lbb['items'] = $sql->paginate(6);
      
      return view('frontend.donation')->with($lbb);
    }
    
    public function gift(){
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'gift';
      $lbb['items'] = Item::where('code','!=','SCH')
              ->where('is_active',1)
              ->paginate(6);
      return view('frontend.gift')->with($lbb);
    }
    
    public function detail($type ,$id){
      $lbb['config'] = API::getDefaultConfig();
      $lbb['session'] = Session::get(null);
      $lbb['page_type'] = 'item_detail';
      $lbb['item_type'] = $type;
      if($type == 'donation'){
        $items = Student::where('id','=',$id)->get();
        if(count($items)>0){
          $lbb['item'] = $items[0];
          $lbb['item']->code = 'SCH';
          $lbb['item']->product_id = 1;
          $lbb['item']->product_name = 'Student Schoolarship';
        }
        
        
        $price_detail = PriceList::join('price_list_lines', 'price_list_lines.price_list_id', '=', 'price_lists.id')
                        ->where('start_year', '<=', date('Y'))
                        ->where('end_year','>=', date('Y'))
                        ->where('price_list_lines.school_code',$lbb['item']->school_code)
                        ->limit(1)->get();
        
        if(count($price_detail)>0){
          $lbb['price_detail'] = $price_detail[0];
        }else{
          $lbb['price_detail'] = null;
        }
      }else{
        $items = Item::where('id','=',$id)->get();
        
        if(count($items)>0){
          $lbb['item'] = $items[0];
        }
       
      }

      return view('frontend.item_detail')->with($lbb);
      
    }
    
}
