<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SubscriptionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subs_plan', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 30);
            $table->integer('store_number');
            $table->integer('duration_day');
            $table->integer('price');

            $table->timestamps();
        });

        Schema::create('subs_transaction', function(Blueprint $table) {
            $table->increments('id');
            $table->dateTime('date');
            $table->dateTime('subs_end')->nullable();
            $table->string('payment_method', 80);
            $table->string('payment_status', 80)->nullable();
            $table->string('order_id',50)->nullable();
            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')->references('id')->on('owner')->onDelete('cascade');
            $table->integer('subs_plan_id')->unsigned();
            $table->foreign('subs_plan_id')->references('id')->on('subs_plan');
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
        Schema::dropIfExists('subs_transaction');
        Schema::dropIfExists('payment_method');
        Schema::dropIfExists('subs_plan');
    }
}
