<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class StoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('store', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);

            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')->references('id')->on('owner')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('staff', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('salary')->nullable();

            $table->integer('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('store')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('cost', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->integer('amount');
            $table->dateTime('date');

            $table->integer('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('store')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('category', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);

            $table->integer('owner_id')->unsigned();
            $table->foreign('owner_id')->references('id')->on('owner')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('sub_category', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);

            $table->integer('category_id')->unsigned();
            $table->foreign('category_id')->references('id')->on('category')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('product', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('sku', 30)->nullable();
            $table->string('note', 50)->nullable();
            $table->integer('capital_price')->nullable();

            $table->integer('sub_category_id')->unsigned();
            $table->foreign('sub_category_id')->references('id')->on('sub_category')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('store_product', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('selling_price');

            $table->integer('store_id')->unsigned();
            $table->foreign('store_id')->references('id')->on('store')->onDelete('cascade');
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->unique(['store_id', 'product_id']);
            $table->timestamps();
        });

        Schema::create('transaction', function(Blueprint $table) {
            $table->increments('id');
            $table->string('invoice', 20);
            $table->dateTime('date');
            $table->string('note', 50)->nullable();
            $table->integer('amount');
            $table->integer('status_id')->unsigned();
            $table->foreign('status_id')->references('id')->on('status');
            $table->integer('staff_id')->unsigned();
            $table->foreign('staff_id')->references('id')->on('staff')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('transaction_product', function(Blueprint $table) {
            $table->increments('id');
            $table->integer('qty');
            $table->string('note',50)->nullable();
            $table->integer('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->integer('transaction_id')->unsigned();
            $table->foreign('transaction_id')->references('id')->on('transaction')->onDelete('cascade');
            $table->unique(['product_id', 'transaction_id']);
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
        Schema::dropIfExists('transaction_product');
        Schema::dropIfExists('transaction');
        Schema::dropIfExists('store_product');
        Schema::dropIfExists('product');
        Schema::dropIfExists('sub_category');
        Schema::dropIfExists('category');
        Schema::dropIfExists('cost');
        Schema::dropIfExists('staff');
        Schema::dropIfExists('store');
    }
}
