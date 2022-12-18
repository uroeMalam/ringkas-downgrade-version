<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AudioModel extends Model
{
    // use HasFactory;

    protected $table = "tb_audios";

    protected $fillable = [
      'uploader',
      'title',
      'audio_url',
      'start_at',
      'detail',
      'full_text',
      'most_occur',
      'url_wordcloud'
    ];

    protected $casts = [
      'detail' => 'array',
      'most_occur' => 'array',
      'start_at' => 'datetime',
    ];
  
    protected $hidden = [
      'created_at',
      'updated_at',
    ];
}
