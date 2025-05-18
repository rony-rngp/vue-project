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
        Schema::create('campaign_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('campaigns')->onDelete('cascade');
            $table->string('number');  // Directly store the phone number
            $table->enum('status', ['pending', 'dialed', 'answered', 'failed'])->default('pending');
            $table->string('dtmf_input')->nullable();  // Store any DTMF input (if any)
            $table->timestamp('called_at')->nullable();
            $table->timestamps();

            // Index to improve query speed
            $table->index('status');
            $table->index('campaign_id');
            $table->index('number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_calls');
    }
};
