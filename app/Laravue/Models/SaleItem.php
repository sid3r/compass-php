<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
  public $timestamps = true;
  protected $fillable = ['sale_id', 'product_id', 'qty'];
  protected $hidden = ['created_at','updated_at','deleted_at'];

  public function sale(){
    return $this->belongsTo('App\Laravue\Models\Sale');
  }
  public function product(){
    return $this->belongsTo('App\Laravue\Models\Product');
  }
}
