<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Generic\GenericController;
use App;
class UserReportController extends GenericController
{
    function __construct(){
        $this->model = new App\Models\UserReport();
        $this->tableStructure = [
          'columns' => [
          ],
          'foreign_tables' => [
          ]
        ];
        $this->initGenericController();
    }
    function create(Request $request){
        $requestData = $request->all();
        $resultObject = $this->createUpdateEntry($requestData);
        if($resultObject['success'] && config('app.MAIL_MAILER') === 'smtp'){
            $this->responseGenerator->addDebug('MAIL_MAILERPass', config('app.MAIL_MAILER'));
            $user = (new App\Models\User())->find($this->userSession('id'))->get()->toArray();
            $data = [
                'username' => $user->username,
                'email' => $user->email,
                'detail' => $requestData['detail']
            ];
            Mail::send('report-email', $data, function($message) use ($requestData) {
                $message->to('noreply@thinka.io')
                ->subject('Report Submitted');
                $message->from('noreply@thinka.io','Thinka');
            });
        }else{
            $this->responseGenerator->addDebug('MAIL_MAILERFailed', config('app.MAIL_MAILER'));
        }
        $this->responseGenerator->setSuccess($resultObject['success']);
        $this->responseGenerator->setFail($resultObject['fail']);
        return $this->responseGenerator->generate();
    }
}
