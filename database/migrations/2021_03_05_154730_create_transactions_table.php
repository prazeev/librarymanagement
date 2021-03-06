<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('type')->default('borrow');
            $table->text('notes')->nullable();
            $table->dateTimeTz('expected_closure');
            $table->boolean('resolved')->default(false);
            $table->timestamps();
        });
      Schema::create('transaction_books', function (Blueprint $table) {
        $table->integer('book_id');
        $table->integer('transaction_id');
      });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
