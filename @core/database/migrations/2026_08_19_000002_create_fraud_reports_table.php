<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFraudReportsTable extends Migration
{
    public function up()
    {
        Schema::create('fraud_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('causes')->onDelete('cascade');
            $table->integer('fraud_score')->default(0);
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->enum('status', ['pending', 'reviewed', 'cleared', 'flagged'])->default('pending');
            $table->json('check_results')->nullable();
            $table->string('recommendation')->nullable();
            $table->text('admin_notes')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamps();

            $table->index('risk_level');
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('fraud_reports');
    }
}
