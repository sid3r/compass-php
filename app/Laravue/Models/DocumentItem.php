<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentItem extends Model
{
    protected $fillable = [
      'document_id', 'designation', 'quantity', 'unit', 'unit_price', 'discount'
    ];
    protected $hidden = ['created_at','updated_at','deleted_at'];
}
