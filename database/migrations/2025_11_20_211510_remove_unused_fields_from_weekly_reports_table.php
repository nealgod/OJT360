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
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->dropColumn(['activities_accomplished', 'skills_learned', 'challenges_faced']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('weekly_reports', function (Blueprint $table) {
            $table->text('activities_accomplished')->nullable();
            $table->text('skills_learned')->nullable();
            $table->text('challenges_faced')->nullable();
        });
    }
};
