<?php

namespace App\Http\Middleware;


use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Jenssegers\Agent\Agent;
use GeoIp2\Database\Reader;

class SessionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){

      /**
       * Bypass this middleware if comes from certain route name
       */
      if(in_array($request->route()->getName(), [ 'image-route' ])) return $next($request);


      /**
       * Initialize session, assign default value
       */
      $session = $request->session()->get(null);
      
      $session['locale'] = 'id';
      $session['ip_address'] = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
      $session['version'] = '11.2.33.4.4';
//      if(!isset($session['locale'])){
//        $session['locale'] = 'id';
//      }
//      if(isset($_GET['locale']) && in_array($_GET['locale'], [ 'id', 'en' ]))
//        $session['locale'] = $_GET['locale'];
      
      /**
       * Set APP locale
       */
      app()->setLocale($session['locale']);

      $request->session()->put($session);


      $response = $next($request);

      //Request::header('Cache-Control', 'public, max-age=7200');
      //$response->header('Cache-Control', 'public, max-age=7200');

      /**
       * Always enable CORS
       */
      if(method_exists($response, 'header')){
        $headers = [
          'Access-Control-Allow-Origin'=> '*',
          'Access-Control-Allow-Methods'=> 'POST, GET, OPTIONS, PUT, DELETE',
          'Access-Control-Allow-Headers'=> 'Content-Type, X-Auth-Token, Origin'
        ];
        foreach($headers as $key => $value)
          $response->header($key, $value);
      }

      /**
       * Logging for API request
       */
      if(strpos($request->url(), '/api/') !== false &&
        (strpos($request->header('user-agent'), 'Flower') !== false ||
          strpos($request->header('x-user-agent'), 'Flower') !== false)){

        $timestamp = date('YmdHis');
        $remoteip = $_SERVER['REMOTE_ADDR'];
        $user_agent = $request->header('x-user-agent') != null ? $request->header('x-user-agent') : $request->header('user-agent');
        $method = $request->method();
        $url = $request->url();
        $request_content = $request->method() == 'GET' ? json_encode($request->query()) : json_encode(json_decode($request->getContent()));
        $response_content = json_encode(json_decode($response->getContent()));
        $sessionid = $request->session()->get('id');
        $sessionid = $sessionid == null ? 'Unknown sessionid' : $sessionid;

        DB::table('api_log')->insert([
          'timestamp'=>$timestamp,
          'sessionid'=>$sessionid,
          'remoteip'=>$remoteip,
          'user_agent'=>$user_agent,
          'method'=>$method,
          'url'=>$url,
          'exec_time'=>microtime(1) - LARAVEL_START,
          'request_content'=>$request_content,
          'response_content'=>$response_content
        ]);

      }

      return $response;

    }
}

