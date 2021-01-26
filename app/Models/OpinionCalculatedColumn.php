<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Generic\GenericModel;

class OpinionCalculatedColumn extends Model
{
    use HasFactory;
    public function opinion(){
        return $this->belongsTo('App\Models\Opinion');
    }
    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing(str_plural($this->getTable()));
    }
}
