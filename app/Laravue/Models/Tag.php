<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
  protected $fillable = [ 'name', 'color', 'native'];
  protected $hidden = ['created_at','updated_at','deleted_at'];

  public function companies(){
    return $this->belongsToMany('App\Laravue\Models\Company', 'company_tags', 'tag_id', 'company_id');
  }
}
