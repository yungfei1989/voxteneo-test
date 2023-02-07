<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
  protected $table = 'configuration';
  protected $guarded = ['id'];
  protected $fillable = [];

}