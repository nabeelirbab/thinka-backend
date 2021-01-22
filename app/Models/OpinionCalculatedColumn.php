<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class OpinionCalculatedColumn extends GenericModel
{
    use HasFactory;
    public function opinion(){
        return $this->belongsTo('App\Models\Opinion');
    }
}
