<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('order_product', function (Blueprint $table) {
            $table->primary(['order_id', 'product_id']);
            $table->foreignId('order_id')->references('id')->on('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_product');
    }
};
