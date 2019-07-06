<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('fname', 64);
            $table->string('mname', 64)->nullable();
            $table->string('lname', 64);
            $table->string('email', 64)->unique();
            $table->string('avatar')->nullable();
            $table->string('password');
            $table->string('email_verification_code')->unique();

            // dates
            $table->timestamp('email_verified_at')->nullable();

            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
}
