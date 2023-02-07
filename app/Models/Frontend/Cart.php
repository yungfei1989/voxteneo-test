<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
  protected $table = 'cart';
  protected $guarded = ['id'];
  protected $fillable = [];

  const UPDATED_AT = 'modifiedon';
  const CREATED_AT = 'createdon';
  
}