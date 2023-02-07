<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class RacunShopee extends Model
{
  protected $table = 'racun_shopee';
  protected $guarded = ['id'];
  protected $fillable = [];
}