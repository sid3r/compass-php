<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyTags extends Model
{
  protected $fillable = ['company_id', 'tag_id'];
  // protected $table = ['company_tags'];
  protected $hidden = ['created_at','updated_at','deleted_at'];
}
