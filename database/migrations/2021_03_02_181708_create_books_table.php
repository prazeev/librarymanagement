<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->dateTimeTz('date_added');
            $table->string('author')->nullable();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->dateTimeTz('publication_date')->nullable();
            $table->string('location')->nullable();
            $table->string('isbn')->nullable();
            $table->integer('quantity')->default(1);
            $table->string('book_cover')->nullable();
            $table->longText('keywords')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
