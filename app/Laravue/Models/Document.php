<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{

    protected $fillable = [
        'type', 'status', 'code', 'company_id', 'date', 'vatrate', 'stamprate', 'paid', 'observations', 'user_id'
    ];

    public function items(){
        return $this->hasMany('App\Laravue\Models\DocumentItem');
    }

    public function company(){
        return $this->belongsTo('App\Laravue\Models\Company');
    }
    
    public function user(){
        return $this->belongsTo('App\Laravue\Models\User');
    }
}
