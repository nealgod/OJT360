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
        Schema::table('acceptance_letters', function (Blueprint $table) {
            // Drop foreign key constraint before dropping acceptance_requests table
            try {
                $table->dropForeign(['acceptance_request_id']);
            } catch (\Exception $e) {
                // Ignore if foreign key doesn't exist
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
        Schema::table('acceptance_letters', function (Blueprint $table) {
            // Restore foreign key if rollback
            $table->foreign('acceptance_request_id')
                  ->references('id')
                  ->on('acceptance_requests')
                  ->onDelete('cascade');
        });
    }
};
