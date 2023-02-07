<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
  protected $table = 'slider';
  protected $guarded = ['id'];
  protected $fillable = [];

}