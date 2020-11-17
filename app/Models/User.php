<?php

namespace App\Models;
use App\Generic\GenericModel as GenericModel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends GenericModel
{
    use HasFactory, Notifiable;

    // protected $fillable = ['user_id', 'first_name', 'middle_name', 'last_name', 'mobile_number', 'gender', 'birthdate', 'occupation'];
    protected $validationRules = [
      'email' => 'required|email|unique:users,email,except,id',
      'password' => 'required|min:3',
      'pin' => 'required|size:4'
    ];
    protected $defaultValue = [
      'middle_name' => ''
    ];
    protected $validationRuleNotRequired = ['username', 'middle_name', 'status', 'user_type_id'];
    public function systemGenerateValue($data){
      (isset($data['email'])) ? $data['username'] = $data['email'] : null;
      (isset($data['password'])) ? $data['password'] = Hash::make($data['password']) : null;
      if((!isset($data['id']) || $data['id'] == 0) && !isset($data['status'])){ // if create
        $data['status'] = 1;
      }
      return $data;
    }
    public function user_basic_information(){
      return $this->hasOne('App\Models\UserBasicInformation');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
