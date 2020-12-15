<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;
class NotificationUser extends GenericModel
{
    use HasFactory;
    public function notification(){
        return $this->belongsTo('App\Models\Notification');
    }
}
