<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnExtraToMobileSlidersTable extends Migration
{

    public function up()
    {
        Schema::table('mobile_sliders', function (Blueprint $table) {
            $table->bigInteger('donation_id')->nullable();
            $table->text('subtitle')->nullable();
        });
    }


    public function down()
    {
        Schema::table('mobile_sliders', function (Blueprint $table) {
            $table->dropColumn('donation_id');
            $table->dropColumn('subtitle');
        });
    }
}
