<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
  protected $table = 'config';
  protected $fillable = array('name', 'group', 'value');
  protected $hidden = ['created_at','updated_at','deleted_at'];
}
