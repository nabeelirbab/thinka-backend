<?php

namespace App\Models;

use App\Generic\GenericModel;

use Illuminate\Database\Eloquent\Model;

class CompanyUser extends GenericModel
{
  protected $fillable = [
    'user_id',
    'company_id',
    'status'
  ];
  public function systemGenerateValue($entry)
  {
    if (!isset($entry['id']) || $entry['id'] == null) {
      $entry['status'] = 0;
    }
    return $entry;
  }
  public function company()
  {
    return $this->belongsTo('App\Models\Company', 'company_id', 'id');
  }
  public function user_basic_information()
  {
    return $this->belongsTo('App\Models\UserBasicInformation', 'user_id', 'user_id');
  }
  public function user_profile_photo()
  {
    return $this->hasOne('App\Models\UserProfilePhoto', 'user_id', 'user_id');
  }
}
