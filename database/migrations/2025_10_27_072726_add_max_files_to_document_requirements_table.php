<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->integer('max_files_per_submission')->default(2)->after('max_file_size_mb');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->dropColumn('max_files_per_submission');
        });
    }
};
