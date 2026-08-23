<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEvidenceToFraudReportsTable extends Migration
{
    public function up()
    {
        Schema::table('fraud_reports', function (Blueprint $table) {
            $table->json('evidence')->nullable()->after('check_results');
        });
    }

    public function down()
    {
        Schema::table('fraud_reports', function (Blueprint $table) {
            $table->dropColumn('evidence');
        });
    }
}
