<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');                 // income | expense
            $table->string('category');             // e.g. Salaries, Visa service
            $table->decimal('amount', 12, 2);
            $table->date('occurred_on');
            $table->string('method')->nullable();   // Cash, Bank, bKash…
            $table->string('reference')->nullable(); // voucher / txn id
            $table->text('note')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // related client (optional)
            $table->timestamps();

            $table->index(['type', 'occurred_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
