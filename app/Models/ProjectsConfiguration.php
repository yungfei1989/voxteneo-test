<?php

namespace App\Models;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class ProjectsConfiguration extends Model
{
  protected $table = 'projects_configuration';
  protected $guarded = ['id'];
  protected $fillable = [];

}