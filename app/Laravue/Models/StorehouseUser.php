<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class StorehouseUser extends Model
{
  protected $fillable = ['storehouse_id', 'user_id'];
  protected $hidden = ['created_at','updated_at','deleted_at'];
}
