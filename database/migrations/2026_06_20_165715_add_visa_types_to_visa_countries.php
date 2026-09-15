<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->json('visa_types')->nullable()->after('subtitle');
        });

        // Backfill existing rows with a sensible default list.
        \App\Models\VisaCountry::whereNull('visa_types')->get()->each(function ($c) {
            $c->update(['visa_types' => ['Tourist', 'Business']]);
        });
    }

    public function down(): void
    {
        Schema::table('visa_countries', function (Blueprint $table) {
            $table->dropColumn('visa_types');
        });
    }
};
