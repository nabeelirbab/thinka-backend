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

/* File Server */
Route::get('v1/check-upload-ticket', "App\FileServer\Controllers\UploadTicketController@index");
Route::post('v1/get-ticket', "App\FileServer\Controllers\UploadTicketController@getTicket");
Route::post('v1/upload', "App\FileServer\Controllers\FileListController@upload");

Route::get('files/{filename}', function ($filename)
{
  $explodedFileName = explode('.', $filename);
  $path = storage_path('app/uploaded_files/'.$explodedFileName[1].'/' . $filename);
  $fileList = (new App\FileServer\Models\FileList())->where('name', $filename);
  if(!$fileList->count()){
    //TODO prevent brute force huessing of links
    abort(404);
  }
  if (!File::exists($path)) {
      abort(404);
  }

  $file = File::get($path);
  $type = File::mimeType($path);

  $response = Response::make($file, 200);
  $response->header("Content-Type", $type);
  $fileAccessHistory = new App\FileServer\Models\FileAccessHistory();
  $fileAccessHistory->name = $filename;
  $fileAccessHistory->ip_address = getenv('REMOTE_ADDR');
  $fileAccessHistory->save();
  return $response;
});

