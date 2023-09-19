<?php

namespace App\Laravue\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['name', 'tel', 'fax', 'email', 'address', 'region', 'nif', 'rc', 'ai'];
    protected $hidden = ['created_at','updated_at','deleted_at'];
    // protected $appends = ['tags'];
    
    public function contacts(){
      return $this->hasMany('App\Laravue\Models\Contact');
    }
    
    public function documents(){
      return $this->hasMany('App\Laravue\Models\Document');
    }

    public function tags(){
      return $this->belongsToMany('App\Laravue\Models\Tag', 'company_tags', 'company_id', 'tag_id');
    }

    // public function getTagsAttribute(){
    //   return $this->belongsToMany('App\Laravue\Models\Tag', 'company_tags', 'company_id', 'tag_id');
    // }
}
