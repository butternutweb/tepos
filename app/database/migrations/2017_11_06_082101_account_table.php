<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('status', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30);

            $table->timestamps();
        });

        Schema::create('account', function(Blueprint $table) {
            $table->increments('id');
            $table->string('username', 50)->unique(); // Using prefix for staff (<owner_username>_)
            $table->string('password', 60);
            $table->string('email')->unique();
            $table->string('name', 30);
            $table->string('phone', 30);

            $table->dateTime('last_login')->nullable();
            $table->string('remember_token', 60)->nullable();
            $table->string('verification_token', 60)->nullable();
            $table->dateTime('verification_token_end')->nullable();
            $table->string('changeemail_token', 60)->nullable();
            $table->dateTime('changeemail_token_end')->nullable();
            $table->string('forgot_token', 60)->nullable();
            $table->dateTime('forgot_token_end')->nullable();
            $table->integer('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('status');
            $table->integer('child_id')->unsigned();
            $table->string('child_type');
            $table->unique(['child_id', 'child_type']);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('owner', function(Blueprint $table) {
            $table->increments('id');

            $table->timestamps();
        });

        Schema::create('admin', function(Blueprint $table) {
            $table->increments('id');

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
        Schema::dropIfExists('admin');
        Schema::dropIfExists('owner');
        Schema::dropIfExists('account');
        Schema::dropIfExists('status');
    }
}
