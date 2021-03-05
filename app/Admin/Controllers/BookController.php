<?php

namespace App\Admin\Controllers;

use App\Models\Book;
use App\Models\Course;
use App\Models\MediaType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Collapse;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class BookController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Book';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid() {
      $grid = new Grid(new Book());
      $grid->actions(function ($actions) {
        $actions->disableView();
      });
      $grid->filter(function($filter){
        $filter->disableIdFilter();
        $filter->like('title', 'Name');
        $filter->where(function ($query) {
          $query->whereHas('medias', function ($query) {
            $query->where('title', 'like', "%{$this->input}%");
          });
        }, __('Media Type'));
        $filter->where(function ($query) {
          $query->whereHas('courses', function ($query) {
            $query->where('course', 'like', "%{$this->input}%");
          });
        }, __('Course'));
      });

      $grid->column('id', __('Id'));
        $grid->column('isbn', __('ISBN'))->prefix('#')->modal(__('QR Code'), function ($model) {
          return "<center>".QrCode::size(300)->generate('codingdriver.com').'</center>';
        });
        $grid->column('medias', __('Media types'))->display(function ($medias) {
          $medias = array_map(function ($media) {
            return "{$media['title']}";
          }, $medias);
          return join(', ', $medias);
        });
        $grid->column('title', __('Title'))->modal(__('Details'), function ($model) {
          $tab = new Tab();
          // Desc
          $keywords = explode(",", $model->keywords);
          foreach ($keywords as $key => $value) {
            $keywords[$key] = "<span class='label label-success'>{$value}</span>";
          }
          $info = new Table([],[
            [$model->description],
            [join("&nbsp;", $keywords)]
          ]);
          $tab->add(__('Information'), $info->render());
          $attachments = new Collapse();
          $all_attachments = $model->attachments()->get()->map(function ($comment) {
            return $comment->only(['path']);
          })->toArray();
          foreach ($all_attachments as $key => $all_attachment) {
            $file = Storage::disk('public')->url($all_attachment['path']);
            $all_attachments[$key][count($all_attachment)] = '<a href="'.$file.'" target="_blank">Download</a>';
          }
          if(empty($model->book_cover)) {
            $cover = 'No cover picture.';
          } else {
            $image = Storage::disk('public')->url($model->book_cover);
            $cover = "<img src='".$image."' class='img img-responsive' />";
          }
          $attachments->add(__('Cover Image'), $cover);
          $attachments->add('All Attachments', new Table([__('Name'),__('Download')], $all_attachments));
          $tab->add(__('Attachments'), $attachments->render());
          // Other details
          $other_details = [];
          array_push($other_details, [
            __('Date added'), $model->date_added,
          ]);
          array_push($other_details, [
            __('Publication added'), $model->publication_date,
          ]);
          array_push($other_details, [
            __('Author'), $model->author,
          ]);
          array_push($other_details, [
            __('Location'), $model->location,
          ]);
          array_push($other_details, [
            __('Original Quantity in Stock'), $model->quantity,
          ]);
          $others = new Table([__('Title'),__('Contents')], $other_details);
          $tab->add(__('Other details'), $others->render());
          return $tab->render();
        });
        $grid->column('author', __('Author'));
//        $grid->column('qr',__('QR Code'))->display(function () {
//          return QrCode::size(50)->generate('codingdriver.com');
//        });
        $grid->column('courses', __('Courses'))->display(function ($courses) {
          $courses = array_map(function ($course) {
            return "<span class='label label-success'>{$course['course']}</span>";
          }, $courses);
          return join('&nbsp;', $courses);
          });
        $grid->column('created_at', __('Created at'))->diffForHumans();

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Book::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('date_added', __('Date added'));
        $show->field('author', __('Author'));
        $show->field('title', __('Title'));
        $show->field('publication_date', __('Publication date'));
        $show->field('location', __('Location'));
        $show->field('isbn', __('Isbn'));
        $show->field('quantity', __('Quantity'));
        $show->field('book_cover', __('Book cover'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Book());
        $form->tab('Basic Info', function ($form) {
          $form->text('title', __('Title'))->required();
          $form->textarea('description',__('Description'))->required();
        })->tab(__('Introduction'), function ($form) {
          $form->datetime('date_added', __('Date added'))->default(date('Y-m-d H:i:s'));
          $form->datetime('publication_date', __('Publication date'))->default(date('Y-m-d H:i:s'));
          $form->text('author', __('Author'))->required();
          $form->text('location', __('Location'));
          $form->text('isbn', __('ISBN'))->required()->placeholder(__("This is unique identifier to book."));
          $form->number('quantity', __('Quantity in stock'))->default(1);
        })->tab(__('Relations'), function ($form) {
          $form->multipleSelect('medias',__('Media Type'))->options(MediaType::all()->pluck('title','id'))->required();
          $form->multipleSelect('courses', __('Courses'))->options(Course::all()->pluck('course','id'))->required();
          $form->tags('keywords',__('Keywords'));
        })->tab(__('Attachments'), function ($form) {
          $form->image('book_cover', __('Book cover'));
          $form->multipleFile('attachments',__('Attachments'))->pathColumn('path')->removable();
        });
        return $form;
    }
}
