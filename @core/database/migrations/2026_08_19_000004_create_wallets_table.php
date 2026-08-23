<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletsTable extends Migration
{
    public function up()
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('campaign_id')->nullable()->constrained('causes')->onDelete('cascade');
            $table->string('address')->unique();
            $table->string('label')->nullable();
            $table->string('network')->default('Demo Ethereum');
            $table->decimal('balance', 15, 8)->default(0);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->index('user_id');
            $table->index('address');
        });
    }

    public function down()
    {
        Schema::dropIfExists('wallets');
    }
}
