<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationsTable extends Migration
{
    public function up()
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('causes')->onDelete('cascade');
            $table->enum('type', ['patient', 'hospital', 'document', 'amount']);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->string('verified_by')->nullable();
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();

            $table->index(['campaign_id', 'type']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('verifications');
    }
}
