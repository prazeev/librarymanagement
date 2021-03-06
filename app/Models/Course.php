<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;
    protected $fillable = ['course'];
  public function books() {
    return $this->belongsToMany(Course::class,'book_courses','book_id','course_id');
  }
}
