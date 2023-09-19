<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model 
{

    public $timestamps = false;
    protected $fillable = array('name', 'category_id', 'image', 'discount', 'unit_price', 'unit_sale_price', 'min_qty', 'description');
    protected $hidden = ['created_at','updated_at','deleted_at'];

    public function category()
    {
      return $this->belongsTo('App\Laravue\Models\Category');
    }
    public function purchases(){
      return $this->hasMany('App\Laravue\Models\PurchaseItem');
    }
    public function sales(){
      return $this->hasMany('App\Laravue\Models\SaleItem');
    }

}