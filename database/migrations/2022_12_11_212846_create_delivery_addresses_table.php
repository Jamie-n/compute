<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::dropColumns('orders', ['name', 'email_address', 'phone_number', 'address_line_1', 'address_line_2', 'city', 'county', 'postcode']);

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email_address');
            $table->string('phone_number');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('city');
            $table->string('county');
            $table->string('postcode');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->foreignId('delivery_address_id')->after('user_id')->references('id')->on('addresses');
        });
    }

    public function down()
    {
        Schema::dropIfExists('delivery_addresses');
    }
};
