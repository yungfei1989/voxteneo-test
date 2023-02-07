<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
  protected $table = 'brand';
  protected $guarded = ['id'];
  protected $fillable = [];

}