<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaType extends Model {
  use HasFactory;
  protected $fillable = ['title'];
  public function books() {
    return $this->belongsToMany(Book::class,'book_media_types','media_type_id','book_id');
  }
}
