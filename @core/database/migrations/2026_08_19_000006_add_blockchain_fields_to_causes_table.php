<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBlockchainFieldsToCausesTable extends Migration
{
    public function up()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->integer('fraud_score')->default(0)->after('monthly_donation_status');
            $table->string('verification_status')->default('unverified')->after('fraud_score');
            $table->string('hospital_name')->nullable()->after('verification_status');
            $table->string('patient_name')->nullable()->after('hospital_name');
            $table->text('medical_details')->nullable()->after('patient_name');
        });
    }

    public function down()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->dropColumn([
                'fraud_score', 'verification_status', 'hospital_name',
                'patient_name', 'medical_details'
            ]);
        });
    }
}
