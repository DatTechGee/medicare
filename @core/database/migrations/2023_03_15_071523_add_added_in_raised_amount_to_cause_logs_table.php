<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddedInRaisedAmountToCauseLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	
        Schema::table('cause_logs', function (Blueprint $table) {
	if(!Schema::hasColumn('cause_logs','added_in_raised_amount')){
	 $table->bigInteger('added_in_raised_amount')->default(0);
	}
           
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cause_logs', function (Blueprint $table) {
            if(Schema::hasColumn('cause_logs','added_in_raised_amount')){
             $table->dropColumn('added_in_raised_amount');
            }
        });
    }
}