<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnGiftToCauseLogsTable extends Migration
{
    public function up()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->bigInteger('gift_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->dropColumn('gift_id');
        });
    }
}
