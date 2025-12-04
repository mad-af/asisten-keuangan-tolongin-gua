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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')
                ->constrained('devices')
                ->onDelete('cascade');

            $table->longText('body')->nullable();

            // text | image | file
            $table->string('type')->default('text');

            // simpan array attachment (URL / metadata file)
            $table->json('attachments')->nullable();

            // sent / delivered / read (opsional simulasi)
            $table->string('status')->nullable();

            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
