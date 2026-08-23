<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnTokenToCauseLogsTable extends Migration
{

    public function up()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->string('recuring_token')->nullable();
            $table->boolean('recuring_token_verify')->default(0);
        });
    }

    public function down()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->dropColumn('recuring_token');
            $table->dropColumn('recuring_token_verify');
        });
    }
}
