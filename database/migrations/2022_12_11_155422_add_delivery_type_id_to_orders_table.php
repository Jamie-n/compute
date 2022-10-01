<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_type_id')->after('user_id')->references('id')->on('delivery_types');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign('delivery_type_id_foreign');
            $table->dropColumn('delivery_type_id');
        });
    }
};
