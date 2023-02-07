<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class ProductVariantStock extends Model
{
  protected $table = 'product_variant_stock';
  protected $guarded = ['id'];
  protected $fillable = [];

}