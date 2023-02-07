<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  protected $table = 'product';
  protected $guarded = ['id'];
  protected $fillable = [];

  public function image()
  {
      return $this->hasMany('App\Models\Frontend\ProductImage');
  }
  
  public function mainImage()
  {
      return $this->hasMany('App\Models\Frontend\ProductImage')->where('is_main',1)->limit(1);
  }
  
  public function category()
  {
      return $this->hasOne('App\Models\Frontend\Category','id','category_id');
  }
  
  public function review()
  {
      return $this->hasMany('App\Models\Frontend\ProductReview')->orderBy('id','desc');
  }
  
  public function variants()
  {
      return $this->hasMany('App\Models\Frontend\ProductVariant')->orderBy('variant_name','asc');
  }
  
  public function cheapesPrice(){

    return $obj;

  }
}