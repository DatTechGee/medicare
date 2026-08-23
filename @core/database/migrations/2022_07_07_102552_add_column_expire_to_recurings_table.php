<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnExpireToRecuringsTable extends Migration
{

    public function up()
    {
        Schema::table('recurings', function (Blueprint $table) {
            $table->string('expire_date')->nullable();
        });
    }

    public function down()
    {
        Schema::table('recurings', function (Blueprint $table) {
            $table->dropColumn('expire_date');
        });
    }
}
