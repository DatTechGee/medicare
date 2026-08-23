<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWalletFieldsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('wallet_address', 42)->nullable()->unique()->after('google_id');
            $table->timestamp('wallet_connected_at')->nullable()->after('wallet_address');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['wallet_address', 'wallet_connected_at']);
        });
    }
}
