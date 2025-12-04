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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')
                ->constrained('devices')
                ->onDelete('cascade');

            // pesan asal (opsional)
            $table->foreignId('message_id')
                ->nullable()
                ->constrained('messages')
                ->onDelete('set null');

            $table->decimal('amount', 15, 2); // nilai transaksi
            $table->string('currency', 10)->default('IDR');

            // income | expense
            $table->enum('type', ['income', 'expense']);

            // kategori opsional, bisa nullable
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('categories')
                ->onDelete('set null');

            $table->text('description')->nullable();

            $table->date('date')->nullable();

            // output parser mentah (AI)
            $table->json('raw_parsed')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
