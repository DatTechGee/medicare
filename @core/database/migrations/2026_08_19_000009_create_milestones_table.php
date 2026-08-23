<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMilestonesTable extends Migration
{
    public function up()
    {
        Schema::create('milestones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('causes');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->enum('status', ['pending', 'proof_submitted', 'verified', 'released', 'rejected'])->default('pending');
            $table->text('proof_document')->nullable();
            $table->string('proof_notes')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamp('released_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('admins');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('milestones');
    }
}
