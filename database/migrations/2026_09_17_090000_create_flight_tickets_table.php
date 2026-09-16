<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('flight_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('visa_application_id')->nullable()->constrained()->nullOnDelete();

            // Passenger / booking contact
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone')->nullable();

            // Booking references
            $table->string('pnr');                 // GDS / booking PNR
            $table->string('booking_ref')->nullable(); // airline-specific ref, if any
            $table->string('airline')->nullable(); // primary carrier name

            // Itinerary + travellers (structured JSON)
            $table->json('passengers')->nullable(); // [{name,type,ticket_number,seat}]
            $table->json('segments')->nullable();   // [{airline,flight_number,cabin,from_code,from_city,to_code,to_city,depart_at,arrive_at,baggage}]

            // Fare
            $table->string('currency', 8)->default('BDT');
            $table->decimal('fare_total', 12, 2)->nullable();

            // Original uploaded ticket (stored privately)
            $table->string('source_file_path')->nullable();
            $table->string('source_file_name')->nullable();

            $table->string('status')->default('draft'); // draft, issued, cancelled
            $table->date('issue_date');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'issue_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('flight_tickets');
    }
};
