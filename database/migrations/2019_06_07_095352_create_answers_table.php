<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAnswersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('answers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('user_id')->unsigned();
            $table->bigInteger('question_id')->unsigned();

            $table->text('content');
            $table->string('img_src')->nullable();

            // paid
            $table->float('price', 3, 2)->default(0);
            $table->string('currency')->default('USD');

            // dates
            $table->timestamp('privated_at')->nullable();
            $table->timestamp('is_best_at')->nullable();

            $table->softDeletes();
            $table->timestamps();

            // index
            $table->foreign('user_id')->references('id')->on('users')
                ->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('answers');
    }
}
