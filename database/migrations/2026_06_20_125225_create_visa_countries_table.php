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
        Schema::create('visa_countries', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('flag')->nullable();
            $table->string('subtitle')->nullable();
            $table->string('processing')->nullable();
            $table->string('validity')->nullable();
            $table->string('stay')->nullable();
            $table->string('fee_from')->nullable();
            $table->text('overview')->nullable();
            $table->json('requirements')->nullable();   // string[]
            $table->json('documents')->nullable();       // [{title,desc}]
            $table->string('photo_spec')->nullable();
            $table->json('faqs')->nullable();            // [{q,a}]
            $table->boolean('active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_countries');
    }
};
