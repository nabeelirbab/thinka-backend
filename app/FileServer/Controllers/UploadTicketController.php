<?php

namespace App\FileServer\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\FileServer\Models as App;
use App\Http\Controllers\Controller;
class UploadTicketController extends Controller
{
  public function index(){
    return 'Upload Ticket Contnroller is Okay';
  }
  public function getTicket(Request $request){
    $uploadTicketEntry = [
      "ip_address" => $request->ip(),
      "expected_file_quantity" => $request->input("expected_file_quantity"),
      "note" => $request->input("note")
    ];
    $validator = Validator::make($uploadTicketEntry, [
      "ip_address" => "required",
      "expected_file_quantity" => "required|integer",
      "note" => "required"
    ]);
    if($validator->fails()){
      return response()->json($validator->errors()->toArray(), 422);
    }
    $uploadTicketModel = new App\UploadTicket();
    $uploadTicketModel->ip_address = $uploadTicketEntry['ip_address'];
    $uploadTicketModel->expected_file_quantity = $uploadTicketEntry['expected_file_quantity'];
    $uploadTicketModel->note = $uploadTicketEntry['note'];
    $uploadTicketModel->save();
    if($uploadTicketModel->id){
      return response()->json(['data' => ["id" => $uploadTicketModel->id, 'location' => url('v1/upload')]], 200);
    }else{

    }
  }
}
