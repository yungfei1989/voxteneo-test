<?php

namespace App\Models;

use Illuminate\Http\Request;
use DB;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
  protected $table = 'reservation';
  protected $guarded = ['id'];
  protected $fillable = [];

  const CREATED_AT = 'createdon';
  const UPDATED_AT = null;
}