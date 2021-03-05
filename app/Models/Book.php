<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    public function attachments() {
      return $this->morphMany(Attachment::class,'attachment');
    }
    public function medias() {
      return $this->belongsToMany(MediaType::class,'book_media_types','book_id','media_type_id');
    }
    public function courses() {
      return $this->belongsToMany(Course::class,'book_courses','book_id','course_id');
    }
}
