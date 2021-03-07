<?php
namespace App\Admin\Extensions\Tools;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Tools\AbstractTool;
use Illuminate\Support\Facades\Request;

class ImportBooks extends AbstractTool {
  protected function script()
  {
    $import_url = url(config('admin.route.prefix').'/books/import');
    $return_url = url(config('admin.route.prefix').'/books');
    return <<<EOT
$('#import_book_btn').click(function () {
    $("#import_book_file").click();
});

$('#import_book_file').change(function () {
    var formData = new FormData();
    formData.append('file', $('#import_book_file')[0].files[0]);
    $.ajax({
           url : '{$import_url}',
           type : 'POST',
           data : formData,
           processData: false,
           contentType: false,
           success : function(data) {
              $.pjax({container:'#pjax-container', url: '{$return_url}'})
           },
           error: function(data) {
              $.pjax({container:'#pjax-container', url: '{$return_url}'})
           }
    });
});

EOT;
  }
  public function render() {
    Admin::script($this->script());
    return view('admin.tools.importbooks');
  }
}