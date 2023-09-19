<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
  public $timestamps = true;
  protected $fillable = ['company_id', 'storehouse_id', 'document_id', 'total', 'profit'];

  public function company(){
    return $this->belongsTo('App\Laravue\Models\Company');
  }

  public function storehouse(){
    return $this->belongsTo('App\Laravue\Models\Storehouse');
  }

  public function document(){
    return $this->belongsTo('App\Laravue\Models\Document');
  }

  public function items(){
    return $this->hasMany('App\Laravue\Models\Saleitem');
  }
}
