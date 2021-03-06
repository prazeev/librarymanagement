<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
    'as'            => config('admin.route.prefix') . '.',
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('home');
    $router->get('/api/users', 'ApiController@users');
    $router->get('/api/departments', 'ApiController@departments');
    $router->get('/api/books', 'ApiController@books');
    $router->resource('books', BookController::class);
    $router->resource('media-types', MediaTypeController::class);
    $router->resource('courses', CourseController::class);
    $router->resource('transactions', TransactionController::class);
    $router->resource('students', StudentController::class);
    $router->resource('departments', DepartmentController::class);
});
