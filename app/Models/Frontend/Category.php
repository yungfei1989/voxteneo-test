<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $table = 'category';
  protected $guarded = ['id'];
  protected $fillable = [];

  
  public function parentCategory()
  {
      return $this->hasOne('App\Models\Frontend\ParentCategory','id','parent_category_id');
  }
}