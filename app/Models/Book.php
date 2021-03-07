<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;
    protected $fillable = ['isbn'];
    public function attachments() {
      return $this->morphMany(Attachment::class,'attachment');
    }
    public function medias() {
      return $this->belongsToMany(MediaType::class,'book_media_types','book_id','media_type_id');
    }
    public function courses() {
      return $this->belongsToMany(Course::class,'book_courses','book_id','course_id');
    }
    public function transactions() {
      return $this->belongsToMany(Transaction::class,'transaction_books','book_id','transaction_id');
    }
    public function getInStockAttribute() {
      return $this->quantity - $this->transactions()->where('type','=','borrow')->get()->count() + $this->transactions()->where('type','=','return')->get()->count();
    }
}
