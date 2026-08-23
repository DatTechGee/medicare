<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUserGiftsToGiftsTable extends Migration
{
    public function up()
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->bigInteger('creator_id');
            $table->string('creator_type');
        });
    }

    public function down()
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->dropColumn('creator_id');
            $table->dropColumn('creator_type');
        });
    }
}
