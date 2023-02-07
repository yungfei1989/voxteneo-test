<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class ProductReview extends Model
{
  protected $table = 'product_review';
  protected $guarded = ['id'];
  protected $fillable = [];

  public function customer()
  {
      return $this->hasOne('App\Models\Frontend\Customer','id','customer_id');
  }
  
}