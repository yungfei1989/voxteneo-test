<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
  protected $table = 'customer_address';
  protected $guarded = ['id'];
  protected $fillable = [];

  
}