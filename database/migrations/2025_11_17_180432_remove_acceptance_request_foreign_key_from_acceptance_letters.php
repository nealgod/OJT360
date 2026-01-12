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
        // Try to drop foreign key constraint safely before next steps
        try {
            Schema::table('acceptance_letters', function (Blueprint $table) {
                $table->dropForeign(['acceptance_request_id']);
            });
        } catch (\Exception $e) {
            // Ignore if foreign key doesn't exist or name is incorrect
        }
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
