<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRefundToTransactionType extends Migration
{
    public function up()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::statement("ALTER TABLE blockchain_transactions MODIFY transaction_type enum('donation','withdrawal','release','refund') NOT NULL DEFAULT 'donation'");
    }

    public function down()
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }
        DB::statement("ALTER TABLE blockchain_transactions MODIFY transaction_type enum('donation','withdrawal','release') NOT NULL DEFAULT 'donation'");
    }
}
