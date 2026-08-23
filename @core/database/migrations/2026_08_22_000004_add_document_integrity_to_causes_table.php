<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDocumentIntegrityToCausesTable extends Migration
{
    public function up()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->string('document_hash', 64)->nullable()->after('medical_document');
            $table->timestamp('document_hashed_at')->nullable()->after('document_hash');
            $table->timestamp('document_verified_at')->nullable()->after('document_hashed_at');
        });
    }

    public function down()
    {
        Schema::table('causes', function (Blueprint $table) {
            $table->dropColumn(['document_hash', 'document_hashed_at', 'document_verified_at']);
        });
    }
}
