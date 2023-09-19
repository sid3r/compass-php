<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
  public $timestamps = true;
  protected $fillable = ['purchase_id', 'product_id', 'qty'];
  protected $hidden = ['created_at','updated_at','deleted_at'];

  public function purchase(){
    return $this->belongsTo('App\Laravue\Models\Purchase');
  }
  public function product(){
    return $this->belongsTo('App\Laravue\Models\Product');
  }
}
