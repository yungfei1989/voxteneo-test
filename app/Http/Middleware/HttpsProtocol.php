<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\App;

class HttpsProtocol
{

  public function handle($request, Closure $next)
  {
          if (!$request->secure() && App::environment() === 'production') {
              return redirect()->secure($request->getRequestUri());
          }

          return $next($request); 
  }


  public function terminate($request, $response)
  {
    
    /*if((isset($_GET['debug']) && $_GET['debug'] > 1000 || env('APP_ENV') == 'debug') &&
      isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'html') !== false)
      echo "<script>console.log(" . json_encode($lines) . ")</script>";*/

  }
}
