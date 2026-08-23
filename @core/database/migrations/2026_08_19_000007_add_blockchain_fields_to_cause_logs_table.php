<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlockchainFieldsToCauseLogsTable extends Migration
{
    public function up()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->string('donor_wallet_address')->nullable()->after('added_in_raised_amount');
            $table->string('blockchain_transaction_hash')->nullable()->after('donor_wallet_address');
            $table->string('payment_type')->default('fiat')->after('blockchain_transaction_hash');
            $table->string('donation_status')->default('pending')->after('payment_type');
        });
    }

    public function down()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            $table->dropColumn([
                'donor_wallet_address', 'blockchain_transaction_hash',
                'payment_type', 'donation_status'
            ]);
        });
    }
}
