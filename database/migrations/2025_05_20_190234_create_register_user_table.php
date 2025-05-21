<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegisterUserTable extends Migration
{
    public function up()
    {
        Schema::create('register_user', function (Blueprint $table) {
            $table->id();
            $table->string('mobile')->unique();
            $table->string('name');
            $table->string('email')->unique();
            $table->date('dob');
            $table->string('password');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('register_user');
    }
}

