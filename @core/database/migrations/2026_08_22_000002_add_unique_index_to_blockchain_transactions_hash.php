<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUniqueIndexToBlockchainTransactionsHash extends Migration
{
    public function up()
    {
        Schema::table('blockchain_transactions', function (Blueprint $table) {
            $table->unique('transaction_hash', 'bt_hash_unique');
        });
    }

    public function down()
    {
        Schema::table('blockchain_transactions', function (Blueprint $table) {
            $table->dropUnique('bt_hash_unique');
        });
    }
}
