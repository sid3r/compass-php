<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
  protected $fillable = [
    'name', 'email', 'mobile', 'company_id', 'rating'
  ];
  protected $hidden = ['created_at','updated_at','deleted_at'];

  public function company(){
    return $this->belongsTo('App\Laravue\Models\Company');
  }
}
