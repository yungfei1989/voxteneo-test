<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
  protected $table = 'users';
  protected $guard = 'users';
  protected $guarded = ['id'];
  protected $fillable = [];
  
  const UPDATED_AT = 'modifiedon';
  const CREATED_AT = 'createdon';
  
  

  
}