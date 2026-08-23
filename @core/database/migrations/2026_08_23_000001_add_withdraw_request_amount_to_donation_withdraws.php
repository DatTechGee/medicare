<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddWithdrawRequestAmountToDonationWithdraws extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('donation_withdraws', 'withdraw_request_amount')) {
            Schema::table('donation_withdraws', function (Blueprint $table) {
                $table->decimal('withdraw_request_amount', 15, 2)->default(0)->after('payment_gateway');
            });
        }

        if (!Schema::hasColumn('donation_withdraws', 'transaction_status')) {
            Schema::table('donation_withdraws', function (Blueprint $table) {
                $table->string('transaction_status')->nullable()->after('payment_status');
            });
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement("UPDATE donation_withdraws SET withdraw_amount = withdraw_request_amount WHERE (withdraw_amount IS NULL OR withdraw_amount = '')");
        }
    }

    public function down()
    {
    }
}
