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
        Schema::table('placement_requests', function (Blueprint $table) {
            // Drop the foreign key constraint first
            $table->dropForeign(['company_id']);
            // Modify the column to be nullable
            $table->unsignedBigInteger('company_id')->nullable()->change();
            // Recreate the foreign key constraint
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            // Drop the foreign key constraint
            $table->dropForeign(['company_id']);
            // Make the column not nullable
            $table->unsignedBigInteger('company_id')->nullable(false)->change();
            // Recreate the foreign key constraint
            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
        });
    }
};
