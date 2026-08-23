<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletAddressToCausesTable extends Migration
{
    public function up()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->string('wallet_address', 42)->nullable()->after('medical_details');
        });
    }

    public function down()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->dropColumn('wallet_address');
        });
    }
}
