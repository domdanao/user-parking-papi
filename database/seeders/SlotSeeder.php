<?php

namespace Database\Seeders;

use App\Models\ParkingSlotOwner;
use App\Models\Slot;
use Illuminate\Database\Seeder;

class SlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $owner = ParkingSlotOwner::where('email', 'owner@example.com')->first();

        if (!$owner) {
            return;
        }

        $slots = [
            [
                'name' => 'Slot A1',
                'identifier' => 'A1',
                'location' => json_encode(['latitude' => 14.5995, 'longitude' => 120.9842]),
                'status' => 'available',
                'metadata' => json_encode(['floor' => '1', 'section' => 'A']),
            ],
            [
                'name' => 'Slot A2',
                'identifier' => 'A2',
                'location' => json_encode(['latitude' => 14.5995, 'longitude' => 120.9843]),
                'status' => 'available',
                'metadata' => json_encode(['floor' => '1', 'section' => 'A']),
            ],
            [
                'name' => 'Slot B1',
                'identifier' => 'B1',
                'location' => json_encode(['latitude' => 14.5996, 'longitude' => 120.9842]),
                'status' => 'available',
                'metadata' => json_encode(['floor' => '1', 'section' => 'B']),
            ],
            [
                'name' => 'Slot B2',
                'identifier' => 'B2',
                'location' => json_encode(['latitude' => 14.5996, 'longitude' => 120.9843]),
                'status' => 'available',
                'metadata' => json_encode(['floor' => '1', 'section' => 'B']),
            ],
            [
                'name' => 'Slot C1',
                'identifier' => 'C1',
                'location' => json_encode(['latitude' => 14.5997, 'longitude' => 120.9842]),
                'status' => 'available',
                'metadata' => json_encode(['floor' => '1', 'section' => 'C']),
            ],
        ];

        foreach ($slots as $slot) {
            $owner->slots()->create($slot);
        }
    }
}
