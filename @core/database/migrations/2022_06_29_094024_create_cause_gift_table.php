<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCauseGiftTable extends Migration
{

    public function up()
    {
        Schema::create('cause_gift', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cause_id');
            $table->bigInteger('gift_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cause_gift');
    }
}
