<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('rate_cards', function (Blueprint $table) {
            $table->id();
            $table->integer('hour_block');
            $table->integer('rate');
            $table->timestamps();
        });

        // Insert initial rates as defined in implementation plan
        $rates = [
            ['hour_block' => 1, 'rate' => 60], // First hour of first 2 hours block
            ['hour_block' => 2, 'rate' => 60], // Second hour of first 2 hours block
            ['hour_block' => 3, 'rate' => 40], // 3rd hour
            ['hour_block' => 4, 'rate' => 50], // 4th hour
            ['hour_block' => 5, 'rate' => 60], // 5th hour
            ['hour_block' => 6, 'rate' => 70], // 6th hour
            ['hour_block' => 7, 'rate' => 80], // 7th hour
            ['hour_block' => 8, 'rate' => 90], // 8th hour
            ['hour_block' => 9, 'rate' => 100], // After 8th hour
        ];

        foreach ($rates as $rate) {
            DB::table('rate_cards')->insert([
                'hour_block' => $rate['hour_block'],
                'rate' => $rate['rate'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_cards');
    }
};
