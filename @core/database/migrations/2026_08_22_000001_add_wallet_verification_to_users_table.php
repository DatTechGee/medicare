<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletVerificationToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('wallet_verified')->default(false)->after('wallet_connected_at');
            $table->timestamp('wallet_verified_at')->nullable()->after('wallet_verified');
            $table->unsignedBigInteger('wallet_verified_by')->nullable()->after('wallet_verified_at');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wallet_verified', 'wallet_verified_at', 'wallet_verified_by']);
        });
    }
}
