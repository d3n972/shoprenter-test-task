<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/*
 * @property string hash
 * @property string secretText
 * @property string createdAt -- datetime
 * @property string expiresAt -- dt
 * @property integer remainingViews
 */

class Secret extends Model{
  use HasFactory;
  use SoftDeletes;

  const DELETED_AT = 'expires_at';
  const UPDATED_AT = null;

  public static function boot(){
    parent::boot();
    static::deleting(function(Secret $model){
      if(!env('APP_SOFT_DELETE_EXPIRED')){
        $model->forceDelete();
      }
    });
  }

  public function isValid(){
    $is_current = Carbon::parse($this->expires_at)->diffInMinutes(\Carbon\Carbon::now()) >= 1;
    $has_view_tokens = $this->remainingViews >= 1;
   // dd($is_current,Carbon::parse(\Carbon\Carbon::now())->diffInMinutes($this->expires_at),$has_view_tokens);
    if(!$has_view_tokens){
      return false;
    }
    if(!$is_current){
      return false;
    }
    else{
      return $has_view_tokens;
    }


  }


  public function setExpirationTime($input = 0){
    $this->expires_at = Carbon::now()->addMinutes($input);
  }

  public function setSecret($data){
    if(env('APP_SECRETS_ARE_ENCRYPTED')){
      throw new \ErrorException('Not yet implemented');
    }
    else{
      return $this->secret = $data;
    }
  }

  public function getSecret(){
    if(env('APP_SECRETS_ARE_ENCRYPTED')){
      throw new \ErrorException('Not yet implemented');
    }
    else{
      return $this->secret;
    }
  }
}
