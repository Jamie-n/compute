<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30);
            $table->integer('discount_percentage');
            $table->dateTime('code_active_start');
            $table->dateTime('code_active_end');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discount_codes');
    }
};
