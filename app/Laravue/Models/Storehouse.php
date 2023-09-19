<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Storehouse extends Model
{
  public $timestamps = false;
  protected $fillable = ['name', 'code', 'adress', 'status', 'user_id'];
  protected $hidden = ['created_at','updated_at','deleted_at'];

  public function purchases() {
    return $this->hasMany('App\Laravue\Models\Purchase');
  }
  public function sales() {
    return $this->hasMany('App\Laravue\Models\Sale');
  }
  public function users(){
    return $this->belongsToMany('App\Laravue\Models\User', 'storehouse_users', 'storehouse_id', 'user_id');
  }
  
}
