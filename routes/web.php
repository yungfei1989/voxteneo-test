<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

$vox_routes = function(){
  
    

  Route::get('/', ['uses' => 'Vox\LoginController@index', 'as' => 'vox.index' ] );
  Route::post('/login-submit', ['uses' => 'Vox\LoginController@loginSubmit', 'as' => 'vox.loginSubmit' ] );
  Route::post('/signup', ['uses' => 'Vox\LoginController@signup', 'as' => 'vox.signup' ] );
  Route::get('/dashboard', ['uses' => 'Vox\DashboardController@index', 'as' => 'vox.dashboard' ] );
  Route::get('/sport-events', ['uses' => 'Vox\DashboardController@SportEvents', 'as' => 'vox.sportEvents' ] );
  Route::get('/organizers', ['uses' => 'Vox\DashboardController@organizers', 'as' => 'vox.organizers' ] );
  Route::get('/organizers/new', ['uses' => 'Vox\DashboardController@organizersCreate', 'as' => 'vox.organizersCreate' ] );
  Route::get('/organizers/edit/{id}', ['uses' => 'Vox\DashboardController@organizersEdit', 'as' => 'vox.organizersEdit' ] );
  Route::post('/organizers/save', ['uses' => 'Vox\DashboardController@organizersSave', 'as' => 'vox.organizersSave' ] );
  Route::get('/organizers/delete/{id}', ['uses' => 'Vox\DashboardController@organizersDelete', 'as' => 'vox.organizersDelete' ] );
  
  Route::get('/change-password', ['uses' => 'Vox\LoginController@changePassword', 'as' => 'vox.changePassword' ] );
  Route::get('/password/update', ['uses' => 'Vox\LoginController@updatePassword', 'as' => 'vox.changePassword' ] );
  Route::get('/logout', ['uses' => 'Vox\LoginController@logout', 'as' => 'vox.logout' ] );
  
  Route::get('/{any}', function ($any) { return redirect('/', 301); })->where('any', '^(?!captcha).*$');
};
Route::group(['domain'=>'localhost'], $vox_routes);
