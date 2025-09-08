<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            // Drop existing foreign key to alter column
            $table->dropForeign('placement_requests_company_id_foreign');
            // Make column nullable
            $table->unsignedBigInteger('company_id')->nullable()->change();
            // Recreate foreign key allowing null
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('placement_requests', function (Blueprint $table) {
            $table->dropForeign('placement_requests_company_id_foreign');
            $table->unsignedBigInteger('company_id')->nullable(false)->change();
            $table->foreign('company_id')->references('id')->on('companies')->cascadeOnDelete();
        });
    }
};


