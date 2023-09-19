<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model 
{

    protected $table = 'categories';
    protected $hidden = ['created_at','updated_at','deleted_at'];

    protected $fillable = array('name');

    public function parent()
    {
        return $this->belongsTo('App\Laravue\Models\Category', 'parent_id');
    }

    public function children()
    {
        return $this->hasMany('App\Laravue\Models\Category', 'parent_id');
    }

}