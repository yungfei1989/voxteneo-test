<?php

namespace App\Models;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class ProjectsGallery extends Model
{
  protected $table = 'projects_gallery';
  protected $guarded = ['id'];
  protected $fillable = [];

  public function galleryLine()
  {
      return $this->hasMany('App\Models\ProjectsGalleryLine','gallery_id');
  }
}
