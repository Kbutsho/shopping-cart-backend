<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('category');
            $table->integer('quantity');
            $table->integer('price');
            $table->string('image');
            $table->string('details');
            $table->string('sellerName');
            $table->string('sellerPhone');
            $table->integer('sellerId')->unsigned()->index();
        });
        Schema::table('products', function (Blueprint $table) {
            $table->foreign('sellerId')->references('id')->on('sellers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
};
