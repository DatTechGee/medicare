<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisputedToEscrowTable extends Migration
{
    public function up()
    {
        Schema::table('escrow', function (Blueprint $table) {
            $table->unsignedTinyInteger('disputed')->default(0)->after('status');
        });
    }

    public function down()
    {
        Schema::table('escrow', function (Blueprint $table) {
            $table->dropColumn('disputed');
        });
    }
}
