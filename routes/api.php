<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/
$api_resource = function($apiResource){
  $apiResources = (is_array($apiResource))? $apiResource : [$apiResource];
  $namepace = 'App\Http\Controllers\\';
  foreach($apiResources as $apiResourceValue){
    $pascalCase = preg_replace_callback("/(?:^|-)([a-z])/", function($matches) {
        return strtoupper($matches[1]);
    }, $apiResourceValue) . 'Controller';
    Route::get($apiResourceValue."/", $namepace . $pascalCase."@index");
    Route::get($apiResourceValue."/test", $namepace . $pascalCase."@test");
    Route::post($apiResourceValue."/create", $namepace . $pascalCase."@create");
    Route::post($apiResourceValue."/retrieve", $namepace . $pascalCase."@retrieve");
    Route::post($apiResourceValue."/update", $namepace . $pascalCase."@update");
    Route::post($apiResourceValue."/delete", $namepace . $pascalCase."@delete");
  }
};
$custom_api = function($customAPIResource, $method = 'post'){
  $namepace = 'App\Http\Controllers\\';
  for($x = 0; $x < count($customAPIResource); $x++){
    $customAPI = $customAPIResource[$x];
    $splitAPI = explode('/', $customAPIResource[$x]);
    $pascalCase = preg_replace_callback("/(?:^|-)([a-z])/", function($matches) {
      return strtoupper($matches[1]);
    }, $splitAPI[0]) . 'Controller';
    $functionCamelCase = str_replace('-', '', lcfirst(ucwords($splitAPI[1], '-')));
    if($method == 'post'){
      Route::post($customAPI, $namepace . $pascalCase . "@" . $functionCamelCase);
    }else{
      Route::get($customAPI, $namepace . $pascalCase . "@". $functionCamelCase);
    }
  }
};

Route::get('/', function(){
  echo str_plural('registry');
  echo 'API GET';
});
Route::group([
], function ($router) {
  $namepace = 'App\Http\Controllers\\';
  Route::get('/', function(){
    echo 'Auth';
  });
  Route::post('login', $namepace . 'AuthController@login');
  Route::post('logout', $namepace . 'AuthController@logout');
  Route::post('refresh', $namepace . 'AuthController@refresh');
  Route::post('me', $namepace . 'AuthController@me');
  Route::post('user', $namepace . 'AuthController@user');
});

$apiResource = [
  'user',
  'role',
  'user-role',
  'user-access-list',
  'role-access-list',
  'service',
  'service-action',
  'company',
  'statement-type',
  'statement',
  'logic-tree',
  'relation-type',
  'scope',
  'relation',
  'user-relation-bookmark',
  'user-relation-setting'
];
$customAPIResources = [
  'user/request-change-password',
  'user/register',
  'user/confirm-change-password',
  'statement/retrieve-tree',
  'statement/update-relation',
  'relation/trending',
  'relation/publish',
  'relation/delete-all',
  'relation/delete-partial',
  'relation/delete-clip',
  'relation/my-list',
];
$api_resource($apiResource);
$custom_api($customAPIResources);

