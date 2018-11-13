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
            $table->increments('id');
            $table->string('name')->comment('昵称');
            $table->string('remake')->comment('备注');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('openid')->unique();
            $table->string('phone')->unique();
            $table->string('avatar')->comment('头像');
            $table->tinyInteger('sex')->default(1)->comment('性别');
            $table->tinyInteger('identify')->default(3)->comment('身份');
            $table->tinyInteger('status')->default(1)->comment('状态');
            $table->integer('integral')->default(0)->comment('积分');
            $table->rememberToken();
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
