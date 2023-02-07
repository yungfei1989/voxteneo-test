<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
  protected $table = 'banner';
  protected $guarded = ['id'];
  protected $fillable = [];

}