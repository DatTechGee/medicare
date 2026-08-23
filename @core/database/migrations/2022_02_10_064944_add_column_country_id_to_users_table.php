<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


///laravel/fundorex/beta/@core/database/migrations/2022_02_10_064944_add_column_country_id_to_users_table.php
class AddColumnCountryIdToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            if(!Schema::hasColumn('users','country_id')){
                 $table->integer('country_id')->nullable();
            }
           
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            if(Schema::hasColumn('users','country_id')){
                 $table->dropColumn('country_id');
            }
        });
    }
}
