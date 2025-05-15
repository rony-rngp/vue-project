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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('organization_id');
            $table->unsignedBigInteger('number_list_id');
            $table->unsignedBigInteger('voice_file_id');
            $table->string('name');
            $table->enum('status', ['draft', 'running', 'paused', 'completed'])->default('draft');
            $table->json('dtmf_options')->nullable(); // Example: {"1": "Transfer", "2": "Repeat"}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
