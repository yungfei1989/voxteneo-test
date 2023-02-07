<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class ProductVariantValue extends Model
{
  protected $table = 'product_variant_value';
  protected $guarded = ['id'];
  protected $fillable = [];

}