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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('visa_application_id')->nullable()->constrained()->nullOnDelete();

            // Client snapshot
            $table->string('client_name');
            $table->string('client_email');
            $table->string('client_phone')->nullable();

            $table->string('currency', 8)->default('BDT');
            $table->json('items')->nullable();          // [{description, qty, unit_price}]
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->json('payments')->nullable();       // [{date, amount, method, note}]

            $table->string('status')->default('draft'); // draft, sent, partial, paid, cancelled
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['status', 'issue_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
