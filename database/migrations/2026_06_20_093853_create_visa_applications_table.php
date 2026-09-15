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
        Schema::create('visa_applications', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();

            // Trip
            $table->string('country');                 // visa config slug
            $table->string('visa_type')->default('tourist');
            $table->unsignedTinyInteger('travellers')->default(1);
            $table->date('travel_date')->nullable();

            // Applicant (as in passport)
            $table->string('full_name');
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('nationality')->default('Bangladeshi');

            // Passport
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();

            // Contact
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('notes')->nullable();

            // Uploaded documents (private storage paths)
            $table->string('passport_scan_path')->nullable();
            $table->string('photo_path')->nullable();

            $table->string('status')->default('submitted'); // submitted, under_review, docs_required, approved, rejected
            $table->timestamps();

            $table->index(['country', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visa_applications');
    }
};
