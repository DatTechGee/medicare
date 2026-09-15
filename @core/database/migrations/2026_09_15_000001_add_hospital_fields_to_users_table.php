<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddHospitalFieldsToUsersTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'hospital_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('hospital_name')->nullable()->after('role');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'hospital_name')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('hospital_name');
            });
        }
    }
}