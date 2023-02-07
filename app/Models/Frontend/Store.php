<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
  protected $table = 'store';
  protected $guarded = ['id'];
  protected $fillable = [];

}