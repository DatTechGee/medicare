<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnAddressToCauseLogsTable extends Migration
{

    public function up()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->string('address')->nullable();
            $table->string('phone')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->dropColumn('address');
            $table->dropColumn('phone');
        });
    }
}
