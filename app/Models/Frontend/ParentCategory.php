<?php

namespace App\Models\Frontend;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class ParentCategory extends Model
{
  protected $table = 'parent_category';
  protected $guarded = ['id'];
  protected $fillable = [];

  public function category()
  {
      return $this->hasMany('App\Models\Frontend\Category');
  }
}