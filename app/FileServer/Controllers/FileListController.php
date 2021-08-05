<?php

namespace App\FileServer\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Image;

use App\FileServer\Models as App;
use App\Http\Controllers\Controller;
class FileListController extends Controller
{
    public function upload(Request $request){
      $response = [
        "data" => null,
        "error" => []
      ];
      $validator = Validator::make($request->all(), [
        "upload_ticket_id" => "required|integer|exists:upload_tickets,id",
        "files" => "required",
        "files.*" => "max:10000000000|file" //kb - 10 TeraByte
      ]);
      if($validator->fails()){
        $response['error'] = [
          "code" => 1,
          "message" => $validator->errors()->toArray()
        ];
        return response()->json($response, 422);
      }
      $uploadTicket = (new App\UploadTicket())->where('id', $request->input('upload_ticket_id'));
      $response['debug'][] = $request->input('upload_ticket_id');
      $response['debug'][] = $uploadTicket->get()->toArray();
      $UPLOADTICKETLIFE = 1000000;
      if((time() - strtotime(($uploadTicket->get()->toArray())[0]['created_at'])) > $UPLOADTICKETLIFE){ //Check if the upload ticket has expired. Max life is 5 minutes. Enough to send a request
        $response['error'] = [
          "code" => 2,
          "message" => "The upload ticket has expired"
        ];
        $uploadTicket->delete();
        $response['debug'][] = date('Y-m-d H:i:s', time());
        $response['debug'][] = date('Y-m-d H:i:s', strtotime(($uploadTicket->get()->toArray())[0]['created_at']));
        $response['debug'][] = ($uploadTicket->get()->toArray())[0]['created_at'];
        return response()->json($response, 418);
      }
      $attachmentsFiles = $request->file('files');
        $errorsUploading = [];
        $fileUploaded = [];
        foreach($attachmentsFiles as $file){
          $newFile = [
            'upload_ticket_id' => $request->input('upload_ticket_id'),
            'name' => 'na',
            'original_name' => $file->getClientOriginalName(),
            'size' => $file->getSize() / 1000,
            'extension' => $file->getClientOriginalExtension()
          ];
          $fileList = new App\FileList();
          foreach($newFile as $field => $value){
            $fileList->$field = $value;
          }
          if($fileList->save()){
            $newFile['id'] = $fileList->id;
          }else{
            $response["error"] = ["code" => 3, "message" => "Failed to create file list entry"];
            return response()->json($response, 400);
          }
          // printR($file);
          // printR($newFile, $file->getMimeType());
          $newFileName = $this->newFileName($newFile['id'], $request->input('upload_ticket_id')).".".$file->getClientOriginalExtension();
          if($file->storeAs('uploaded_files/'.$file->getClientOriginalExtension(), $newFileName)){
            // $fileList = (new App\FileList())->find();
            $fileList->name = $newFileName;
            $fileList->save();
            $fileUploaded[] = [
              'id' => $fileList->id,
              'name' => $fileList->name
            ];
          }else{
            $errorsUploading[$newFile];
          }
        }
        if(count($errorsUploading)){
          $response["error"] = ["code" => 4, "message" => $errorsUploading];
          return response()->json($response, 409);
        }else{
          $response['data'] = $fileUploaded;
          return response()->json($response, 200);
        }

    }
    private function newFileName($fileListID, $uploadTicketID){
      $fileListID64 = base_convert($fileListID * 1, 10, 32);
      $uploadTicketID64 = base_convert($uploadTicketID, 10, 32) ."";
      for($x = 0; $x < (25 - strlen($uploadTicketID64)); $x++){
        $uploadTicketID64 = "0" . $uploadTicketID64;
      }
      for($x = 0; $x < (25 - strlen($fileListID64)); $x++){
        $fileListID64 = "0" . $fileListID64;
      }
      return substr(md5(microtime()),rand(0,26),5).$fileListID64.substr(md5(microtime()),rand(0,26),5).$fileListID64.substr(md5(microtime()),rand(0,26),5);
    }
    private function isExtensionImage($fileExtension){
      $imageExtensions = ['jpeg', 'png', 'jpg'];
      return isset($imageExtensions[$fileExtension]);
    }
}
