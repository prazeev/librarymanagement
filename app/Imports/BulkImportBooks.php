<?php

namespace App\Imports;

use App\Models\Book;
use App\Models\Course;
use App\Models\MediaType;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;

class BulkImportBooks implements ToModel {
  public $increment = 0;
  protected $columns = [
    'id' => 'Book ID',
    'title' => 'Book Name',
    'author' => 'Author',
    'date_added' => 'Description',
    'description' => 'Publication Date',
    'publication_date' => 'Publication Date',
    'location' => 'Book Location',
    'isbn' => 'ISBN',
    'quantity' => 'Quantity on library',
    'book_cover' => 'Cover Picture',
    'medias' => 'Media Type',
    'courses' => 'Courses',
    'keywords' => 'Tags',
  ];
    public function model(array $row)
    {
      if($this->increment == 0) {
        $this->increment++;
      } else {
        $keys = array_keys($this->columns);
        $book = Book::firstOrNew([
          'isbn' => trim($row[array_search("isbn", $keys)])
        ]);
        foreach ($this->columns as $col => $column) {
          $key = $col;
          $value = trim($row[array_search($col, $keys)]);
          if($col == 'id' || $col == 'book_cover' || $col == 'medias' || $col == 'courses') {
            continue;
          }
          $book->{$key} = $value;
        }
        $book_cover = $row[array_search('book_cover', $keys)];
        if(filter_var($book_cover, FILTER_VALIDATE_URL)) {
          $path = "uploads/".Str::random(40).".png";
          $contents = file_get_contents($book_cover);
          $storage = Storage::put("public/".$path, $contents);
          $book->book_cover = $path;
        }
        $book->save();
        
        $book_medias = $row[array_search('medias', $keys)];
        $book_medias = explode(",", $book_medias);
        $book->medias()->sync([]);
        foreach ($book_medias as $media) {
          if(trim(strlen($media)) == 0) continue;
          $mediaType = MediaType::firstOrNew([
            'title' => trim($media)
          ]);
          $mediaType->title = trim($media);
          $mediaType->save();
          $book->medias()->attach($mediaType->id);
        }
        $book_courses = $row[array_search('courses', $keys)];
        $book_courses = explode(",", $book_courses);
        $book->courses()->sync([]);
        foreach ($book_courses as $book_course) {
          if(trim(strlen($book_course)) == 0) continue;
          $course = Course::firstOrNew([
            'course' => trim($book_course)
          ]);
          $course->course = trim($book_course);
          $course->save();
          $book->courses()->attach($course->id);
        }
        $this->increment++;
        if($book->exists) {
          admin_toastr(__("Successfully updated ".$book->title),'warning');
        } else {
          admin_toastr(__("Successfully imported ".$book->title),'success');
        }
        return $book;
      }
    }
}
