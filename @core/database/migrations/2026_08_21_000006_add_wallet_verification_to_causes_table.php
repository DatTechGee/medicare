<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletVerificationToCausesTable extends Migration
{
    public function up()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->tinyInteger('wallet_verified')->default(0)->after('wallet_address');
            $table->timestamp('wallet_verified_at')->nullable()->after('wallet_verified');
        });
    }

    public function down()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->dropColumn(['wallet_verified', 'wallet_verified_at']);
        });
    }
}
