<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    // use HasFactory;

    protected $table = "tb_level";

    protected $fillable = [
      'level',
      'deskripsi',
    ];
  
    protected $hidden = [
      'created_at',
      'updated_at',
    ];

}
