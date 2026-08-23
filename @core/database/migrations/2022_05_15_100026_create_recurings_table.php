<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecuringsTable extends Migration
{

    public function up()
    {
        Schema::create('recurings', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('cause_log_id')->unsigned();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recurings');
    }
}
