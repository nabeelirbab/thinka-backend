<?php

use App\Http\Controllers\Admin\RelationController;
use App\Http\Controllers\Admin\StatementController;
use App\Http\Controllers\Admin\UserController;
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
Route::match(['get', 'post'], '/admin/login', [UserController::class, 'login'])->name('admin.login');

Route::middleware(['custom.auth', 'admin'])->group(function () {
  Route::get('/admin', [UserController::class, 'index'])->name('admin.dashboard');
  Route::get('/admin/users', [UserController::class, 'manageUsers'])->name('admin.users');
  Route::get('/admin/user/{id}', [UserController::class, 'userDetails'])->name('admin.user.details');
  Route::get('/admin/user/change-status/{id}', [UserController::class, 'changeStatus'])->name('admin.user.changeStatus');
  Route::match(['get', 'post'], '/admin/user/update-password/{id}', [UserController::class, 'updateUserPassword']);
  Route::match(['get', 'post'], '/admin/user/update/{id}', [UserController::class, 'editUser']);
  Route::match(['get', 'post'], '/admin/users/add', [UserController::class, 'register']);
  Route::match(['get', 'post'], '/admin/user-search', [UserController::class, 'searchUser']);

  Route::delete('/admin/users/{id}', [UserController::class, 'deleteUser'])->name('admin.users.delete');
  Route::get('/admin/logout', [UserController::class, 'logout'])->name('admin.logout');

  Route::get('/admin/statemnt-types', [StatementController::class, 'index'])->name('admin.statemnt-types');
  Route::match(['get', 'post'], '/admin/statement-type/{id}', [StatementController::class, 'edit']);

  Route::get('/admin/relation-types', [RelationController::class, 'index'])->name('admin.relation-types');
  Route::match(['get', 'post'], '/admin/relation-type/{id}', [RelationController::class, 'edit']);
});
Route::get('/', function () {
  return view('welcome');
});
Route::get('v1/', function () {
  echo 'Welcome to API v1';
});
Route::post('v1/test-connection', function () {
  echo 'Ok!';
});
Route::get('v1/test-connection', function () {
  echo 'Ok!';
});
Route::post('v1/{module}/{function}', $namepace . "ServiceLayerController@index");

/* File Server */
Route::get('v1/check-upload-ticket', "App\FileServer\Controllers\UploadTicketController@index");
Route::post('v1/get-ticket', "App\FileServer\Controllers\UploadTicketController@getTicket");
Route::post('v1/upload', "App\FileServer\Controllers\FileListController@upload");

Route::get('files/{filename}', function ($filename) {
  $explodedFileName = explode('.', $filename);
  $path = storage_path('app/uploaded_files/' . $explodedFileName[1] . '/' . $filename);
  $fileList = (new App\FileServer\Models\FileList())->where('name', $filename);
  if (!$fileList->count()) {
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


Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
