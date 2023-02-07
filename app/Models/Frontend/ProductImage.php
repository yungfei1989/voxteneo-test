<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
  protected $table = 'product_picture';
  protected $guarded = ['id'];
  protected $fillable = [];

}