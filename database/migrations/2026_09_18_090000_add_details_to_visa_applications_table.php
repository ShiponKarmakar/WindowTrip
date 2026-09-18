<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visa_applications', function (Blueprint $table) {
            $table->string('present_address')->nullable()->after('nationality');
            $table->string('occupation')->nullable()->after('present_address');
            $table->string('purpose')->nullable()->after('occupation');
        });
    }

    public function down(): void
    {
        Schema::table('visa_applications', function (Blueprint $table) {
            $table->dropColumn(['present_address', 'occupation', 'purpose']);
        });
    }
};
