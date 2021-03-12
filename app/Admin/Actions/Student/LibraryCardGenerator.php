<?php
namespace App\Admin\Actions\Student;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class LibraryCardGenerator {
  public $height;
  public $width;
  public $background_color;
  public $background_image;
  public $student;
  public $headerColor = "#003A79";
  public $savingDir = "public/cards";
  protected $qrCode = null;
  protected $cardUrl = "";

  protected $card;

  /**
   * LibraryCardGenerator constructor.
   * @param $height
   * @param $width
   * @param $student
   */
  public function __construct($student) {
    $this->background_color = '#FFFFFF';
    $this->background_image = null;
    $this->student = $student;
    $this->height = 400;
    $this->width = 600;

    $this->card = Image::canvas($this->width, $this->height, $this->background_color);
    $this->qrCode = QrCode::size(150)->format('png')->generate(Crypt::encryptString($this->student->email));
    $qrName = "public/idqr/".Str::random('20').'.png';
    Storage::put($qrName, $this->qrCode);
    $this->qrCode = $qrName;
  }

  /**
   * @param mixed $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }

  /**
   * @param null $background_image
   */
  public function setBackgroundImage($background_image)
  {
    $this->background_image = $background_image;
  }

  /**
   * @param mixed $student
   */
  public function setStudent($student)
  {
    $this->student = $student;
  }

  /**
   * @param string $headerColor
   */
  public function setHeaderColor($headerColor)
  {
    $this->headerColor = $headerColor;
  }

  public function processStudentCard() {
    $header = Image::canvas($this->width, $this->height / 5, $this->headerColor);
    $header->insert(storage_path('app/assets/images/StudentText.png'),'center');
    $this->card->insert($header);;

    $body = Image::canvas($this->width, ($this->height / 5) * 4, "#FFFFFF");
    $body->insert(storage_path('app/'.$this->qrCode),'top-left', 20, 20);

    $name = Image::canvas($this->width - 20 - 150 - 20, 70, "#FFFFFF");
    $name->text($this->student->name, 235,20, function ($font) {
      $font->size(22);
      $font->file(storage_path('app/assets/fonts/OpenSans-Regular.ttf'));
      $font->color($this->headerColor);
      $font->align('right');
    });
    $body->insert($name,"top-left",20+150+20, 20);


    $cardno = Image::canvas($this->width - 20 - 150 - 20, 70, "#FFFFFF");
    $cardno->text("Card #".$this->student->id, 235,20, function ($font) {
      $font->size(22);
      $font->file(storage_path('app/assets/fonts/OpenSans-Regular.ttf'));
      $font->color($this->headerColor);
      $font->align('right');
    });

    $body->insert($cardno,"top-left",20+150+20, 60);



    $this->card->insert($body,'bottom');
    $name = Str::random(10).'.jpg';
    $id_path = $this->savingDir.'/'.$name;
    $photo = $this->card->encode('jpg', 80);
    Storage::put($id_path, $photo);


    $this->cardUrl = Storage::url($id_path);

    return $this->cardUrl;
  }

  /**
   * @return string
   */
  public function getCardUrl()
  {
    return $this->cardUrl;
  }
}