<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToMediaUploadsTable extends Migration
{
    public function up()
    {
        Schema::table('media_uploads', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('dimensions');
            $table->string('type')->nullable()->default('admin')->after('user_id');
        });
    }

    public function down()
    {
        Schema::table('media_uploads', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'type']);
        });
    }
}
