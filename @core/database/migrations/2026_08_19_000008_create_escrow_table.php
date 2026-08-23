<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEscrowTable extends Migration
{
    public function up()
    {
        Schema::create('escrow', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('causes');
            $table->foreignId('cause_log_id')->constrained('cause_logs');
            $table->decimal('amount', 15, 2);
            $table->string('donor_wallet_address', 100)->nullable();
            $table->string('blockchain_tx_hash', 100)->nullable();
            $table->enum('status', ['held', 'released', 'refunded'])->default('held');
            $table->timestamp('released_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('escrow');
    }
}
