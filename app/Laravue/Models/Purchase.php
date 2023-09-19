<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
  public $timestamps = true;
  protected $fillable = ['company_id', 'storehouse_id', 'total'];

  public function company(){
    return $this->belongsTo('App\Laravue\Models\Company');
  }

  public function storehouse(){
    return $this->belongsTo('App\Laravue\Models\Storehouse');
  }

  public function items(){
    return $this->hasMany('App\Laravue\Models\Purchaseitem');
  }
}
