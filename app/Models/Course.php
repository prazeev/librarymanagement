<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
  public function books() {
    return $this->belongsToMany(Course::class,'book_courses','course_id','book_id');
  }
}
