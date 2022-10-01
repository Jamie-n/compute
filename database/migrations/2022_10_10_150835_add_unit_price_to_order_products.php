<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('order_product', function (Blueprint $table) {
            $table->string('unit_price')->after('quantity');
        });
    }

    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('unit_price');
        });
    }
};
