<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnGiftStatusToCausesTable extends Migration
{
    public function up()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->string('gift_status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->dropColumn('gift_status');
        });
    }
}
