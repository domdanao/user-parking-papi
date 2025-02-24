<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\RateCard;
use App\Models\ParkingSlotOwner;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create default rate card template for each existing owner
        ParkingSlotOwner::chunk(100, function ($owners) {
            foreach ($owners as $owner) {
                RateCard::create([
                    'parking_slot_owner_id' => $owner->id,
                    'name' => 'Default Hourly Rate',
                    'description' => 'Standard hourly rate for parking slots',
                    'hour_block' => 1,
                    'rate' => 5000, // ₱50.00
                    'is_active' => true,
                    'is_template' => true,
                ]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove all default rate card templates
        RateCard::where('name', 'Default Hourly Rate')
            ->where('is_template', true)
            ->delete();
    }
};
