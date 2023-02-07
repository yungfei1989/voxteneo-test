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


class DashboardController extends Controller
{
    
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    { 
      if (Session::get('user_id') == '') {
          return redirect::to('/');
      }
        $vox = $this->mandatory();
        $vox['page_type'] = 'dashboard';   
        $vox['page_name'] = 'Admin';
    
        return view('vox.dashboard')->with($vox);
    }
    
    public function organizers()
    { 
      try{
        if (Session::get('user_id') == '') {
          return redirect::to('/');
        }
        $page = isset($_GET['page']) ? $_GET['page'] : '1';
        $per_page = isset($_GET['perPage']) ? $_GET['perPage'] : '20';
        $endpoint = "https://api-sport-events.php6-02.test.voxteneo.com/api/v1/organizers?page=".$page."&perPage=".$per_page;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL,$endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '. Session::get('token'),
        ]);
        $result=curl_exec($ch);
        
        curl_close($ch);
        $content = json_decode($result, true);
        
        $vox = $this->mandatory();
        $vox['page_type'] = 'dashboard';   
        $vox['page_name'] = 'Organizers';
        $vox['data'] = isset($content['data']) ? $content['data'] : [];
        $vox['meta'] = isset($content['meta']) ? $content['meta'] : [];
        $vox['page'] = $page; 
        return view('vox.organizers')->with($vox);
      } catch (\Exception $ex) {
        return ['error' => 1, 'message' => $ex->getMessage().' File: '.$ex->getFile()." Line:".$ex->getLine()];
      }
    }
    
    function organizersDelete($id){
      if (Session::get('user_id') == '') {
          return redirect::to('/');
        }
        $endpoint = "https://api-sport-events.php6-02.test.voxteneo.com/api/v1/organizers/".$id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL,$endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '. Session::get('token'),
        ]);
        $result=curl_exec($ch);
        
        curl_close($ch);
        $content = json_decode($result, true);
        if(isset($content['message'])){
          return redirect('/organizers')->with('message', $content['message']);
        }
        else{
          return redirect('/organizers')->with('message', 'data have been deleted');
        }
    }
    
    function organizersCreate(){
       $vox = $this->mandatory();
        $vox['page_type'] = 'dashboard';   
        $vox['page_name'] = 'Organizers Create';
        return view('vox.organizers-create')->with($vox);
    }
    
    function organizersEdit($id){
      if (Session::get('user_id') == '') {
          return redirect::to('/');
        }
        $endpoint = "https://api-sport-events.php6-02.test.voxteneo.com/api/v1/organizers/".$id;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL,$endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer '. Session::get('token'),
        ]);
        $result=curl_exec($ch);
        
        curl_close($ch);
        $content = json_decode($result, true);
        $vox = $this->mandatory();
        $vox['page_type'] = 'dashboard';   
        $vox['page_name'] = 'Organizers Update';
        $vox['data'] = $content;
        return view('vox.organizers-create')->with($vox);
        
    }
    
    function organizersSave(Request $request){
    try{
        if (Session::get('user_id') == '') {
          return redirect::to('/');
        }
        $params = $request::all();
        
        if(isset($params['id'])){
          $endpoint = "https://api-sport-events.php6-02.test.voxteneo.com/api/v1/organizers/".$params['id'];
          $method = "PUT";
        }
        else{
          $endpoint = "https://api-sport-events.php6-02.test.voxteneo.com/api/v1/organizers";
          $method = "POST";
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
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
          return redirect('organizers')->with('message',$content['message']);
        }
        return redirect('organizers')->with('message','data have been saved');
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
  
}
