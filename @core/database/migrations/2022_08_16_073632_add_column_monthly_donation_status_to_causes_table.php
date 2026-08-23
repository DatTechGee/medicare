<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnMonthlyDonationStatusToCausesTable extends Migration
{
    public function up()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->string('monthly_donation_status')->nullable();
        });
    }

    public function down()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->dropColumn('monthly_donation_status');
        });
    }
}
