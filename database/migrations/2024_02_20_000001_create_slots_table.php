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
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->string('identifier')->unique();
            $table->string('name');
            $table->json('location')->nullable(); // {latitude: number, longitude: number}
            $table->enum('status', ['available', 'occupied', 'unavailable'])->default('available');
            $table->json('metadata')->nullable();
            $table->timestamps();

            // Add indexes for frequently queried fields
            $table->index('status');
            $table->index('identifier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
