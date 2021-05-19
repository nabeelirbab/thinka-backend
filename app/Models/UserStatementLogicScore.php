<?php

namespace App\Models;
use App\Generic\GenericModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserStatementLogicScore extends Model
{
    use HasFactory;
    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }
    public function getTableColumns() {
        return $this->getConnection()->getSchemaBuilder()->getColumnListing(str_plural($this->getTable()));
    }
    
}
