<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockchainTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('blockchain_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cause_log_id')->constrained('cause_logs')->onDelete('cascade');
            $table->foreignId('campaign_id')->constrained('causes')->onDelete('cascade');
            $table->string('wallet_address');
            $table->string('transaction_hash')->unique();
            $table->decimal('amount', 15, 2);
            $table->string('currency')->default('ETH');
            $table->string('network')->default('Demo Ethereum');
            $table->enum('transaction_type', ['donation', 'withdrawal', 'release', 'refund']);
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->string('block_number')->nullable();
            $table->string('gas_fee')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('campaign_id');
            $table->index('transaction_type');
        });
    }

    public function down()
    {
        Schema::dropIfExists('blockchain_transactions');
    }
}
