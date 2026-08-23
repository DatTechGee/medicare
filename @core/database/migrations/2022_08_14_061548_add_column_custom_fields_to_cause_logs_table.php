<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnCustomFieldsToCauseLogsTable extends Migration
{

    public function up()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->longText('custom_fields')->nullable();
            $table->longText('attachments')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
            $table->dropColumn('attachments');
        });
    }
}
