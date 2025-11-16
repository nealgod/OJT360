<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\DocumentRequirement;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add display_order column
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->integer('display_order')->default(999)->after('is_active');
        });
        
        // Set display order for existing requirements
        // Pre-placement requirements
        $appLetter = DocumentRequirement::where('name', 'Application Letter')->first();
        if ($appLetter) {
            $appLetter->update(['display_order' => 1]);
        }
        
        $pds = DocumentRequirement::where('name', 'LIKE', '%Personal Data Sheet%')->first();
        if ($pds) {
            $pds->update(['display_order' => 2]);
        }
        
        $acceptance = DocumentRequirement::where('name', 'LIKE', '%Letter of Acceptance%')->first();
        if ($acceptance) {
            $acceptance->update(['display_order' => 3]);
        }
        
        // Set order for other pre-placement requirements
        $otherPrePlacement = DocumentRequirement::where('type', 'pre_placement')
            ->whereNotIn('name', ['Application Letter', 'Personal Data Sheet (PDS) / Resume', 'Letter of Acceptance'])
            ->orderBy('id')
            ->get();
        
        $order = 10;
        foreach ($otherPrePlacement as $req) {
            $req->update(['display_order' => $order++]);
        }
        
        // Set order for post-placement requirements (starting from 100)
        $postPlacement = DocumentRequirement::where('type', 'post_placement')
            ->orderBy('id')
            ->get();
        
        $order = 100;
        foreach ($postPlacement as $req) {
            $req->update(['display_order' => $order++]);
        }
        
        // Set order for ongoing requirements (starting from 200)
        $ongoing = DocumentRequirement::where('type', 'ongoing')
            ->orderBy('id')
            ->get();
        
        $order = 200;
        foreach ($ongoing as $req) {
            $req->update(['display_order' => $order++]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('document_requirements', function (Blueprint $table) {
            $table->dropColumn('display_order');
        });
    }
};
