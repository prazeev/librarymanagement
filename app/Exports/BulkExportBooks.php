<?php
namespace App\Exports;

use Encore\Admin\Grid;
use Encore\Admin\Grid\Exporters\ExcelExporter;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\WithMapping;

class BulkExportBooks extends ExcelExporter implements WithMapping {
  protected $grid;
  public function __construct(Grid $grid = null) {
    parent::__construct($grid);
    $this->grid = $grid;
  }

  protected $fileName = 'All Books.xlsx';
  protected $columns = [
    'id' => 'Book ID',
    'title' => 'Book Name*',
    'author' => 'Author*',
    'date_added' => 'Date added on store*',
    'description' => 'Description*',
    'publication_date' => 'Publication Date*',
    'location' => 'Book Location*',
    'isbn' => 'ISBN*',
    'quantity' => 'Quantity on library*',
    'book_cover' => 'Cover Picture',
    'medias' => 'Media Type',
    'courses' => 'Categories',
    'keywords' => 'Tags',
  ];

  /**
   * @inheritDoc
   */
  public function map($row): array {
    $media_types = [];
    $courses = [];
    foreach ($row->medias()->get() as $media) {
      array_push($media_types,$media->title);
    }
    foreach ($row->courses()->get() as $course) {
      array_push($courses, $course->course);
    }
    $storage_url = '';
    if(!empty($row->book_cover)) {
      $storage_url = asset(Storage::url($row->book_cover));
    }
    return [
      $row->id,
      $row->title,
      $row->author,
      $row->date_added,
      $row->description,
      $row->publication_date,
      $row->location,
      $row->isbn,
      $row->quantity,
      $storage_url,
      join(", ", $media_types),
      join(", ", $courses),
      $row->keywords,
    ];
  }
}