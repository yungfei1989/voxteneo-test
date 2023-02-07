<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
  protected $table = 'customers';
  protected $guarded = ['id'];
  protected $fillable = [];
  
  const UPDATED_AT = 'modifiedon';
  const CREATED_AT = 'createdon';
  
  public function address()
  {
      return $this->hasMany('App\Models\Frontend\CustomerAddress');
  }
  
}