<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnImageToGiftsTable extends Migration
{

    public function up()
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->string('image')->nullable();
        });
    }

    public function down()
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
}
