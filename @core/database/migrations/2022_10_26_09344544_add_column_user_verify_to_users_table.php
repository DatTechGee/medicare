<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnUserVerifyToUsersTable extends Migration
{

    public function up()
    {
        if(!Schema::hasColumns('users',['user_verify_nid','user_verify_address','user_verify_status'])){

            Schema::table('users', function (Blueprint $table) {
                $table->string('user_verify_nid')->nullable();
                $table->string('user_verify_address')->nullable();
                $table->bigInteger('user_verify_status')->default(0);
            });

        }

    }

    public function down()
    {
        if(!Schema::hasColumns('users',['user_verify_nid','user_verify_address','user_verify_status'])){
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('user_verify_nid');
                $table->dropColumn('user_verify_address');
                $table->dropColumn('user_verify_status');
            });
        }
    }
}
