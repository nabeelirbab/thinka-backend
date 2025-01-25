<?php

namespace App\Models;

use App\Generic\GenericModel;

class UserBasicInformation extends GenericModel
{
  protected $table = 'user_basic_informations';
  protected $appends = ['full_name'];
  protected $validationRuleNotRequired = ['first_name', 'last_name', 'birthdate', 'middle_name', 'website', 'languages', 'title'];
  protected $formulatedColumn = [
    'full_name' => "CONCAT(last_name, ', ', first_name)",
    'full_name_2' => "CONCAT(user_basic_informations.first_name, ' ',user_basic_informations.last_name)",
    // 'full_address' => "CONCAT(address, ', ', city, ', ', province)"
  ];
  public function systemGenerateValue($entry)
  {
    if (!isset($netry['id']) || $entry['id'] == null) {
      $entry['middle_name'] = '';
    }
    return $entry;
  }

  protected $fillable = [
    'first_name',
    'middle_name',
    'last_name',
    'user_id',
  ];
  public function getFullNameAttribute()
  {
    return "{$this->last_name} {$this->first_name}";
  }
}
