<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
  protected $table = 'product_variants';
  protected $guarded = ['id'];
  protected $fillable = [];
  
  public function values()
  {
      return $this->hasMany('App\Models\Frontend\ProductVariantValue')->where('isactive','1')->orderBy('value','asc');
  }
}