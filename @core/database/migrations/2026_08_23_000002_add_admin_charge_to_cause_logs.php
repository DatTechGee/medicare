<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdminChargeToCauseLogs extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('cause_logs', 'admin_charge')) {
            Schema::table('cause_logs', function (Blueprint $table) {
                $table->decimal('admin_charge', 15, 2)->default(0)->after('amount');
            });
        }
    }

    public function down()
    {
    }
}
