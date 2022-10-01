<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('category_product', function (Blueprint $table) {
            $table->primary(['category_id', 'product_id']);
            $table->foreignId('category_id')->references('id')->on('categories');
            $table->foreignId('product_id')->references('id')->on('products');
        });
    }

    public function down()
    {
        Schema::dropIfExists('category_product');
    }
};
