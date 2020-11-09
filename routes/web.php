<?php
use Illuminate\Support\Facades\Route;

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
$namepace = 'App\Http\Controllers\\';
Route::get('/', function () {
    return view('welcome');
});
Route::get('v1/', function(){
  echo 'Welcome to API v1';
});
Route::post('v1/test-connection', function(){
  echo 'Ok!';
});
Route::get('v1/test-connection', function(){
  echo 'Ok!';
});
Route::post('v1/{module}/{function}', $namepace . "ServiceLayerController@index");

