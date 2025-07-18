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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->integer('odoo_contact_id')->nullable();
            $table->string('name');
            $table->string('caller_id')->unique();
            $table->string('email')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->integer('current_ticket')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
