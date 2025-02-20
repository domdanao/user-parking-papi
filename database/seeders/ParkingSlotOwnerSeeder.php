<?php

namespace Database\Seeders;

use App\Models\ParkingSlotOwner;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ParkingSlotOwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ParkingSlotOwner::create([
            'name' => 'Test Owner',
            'email' => 'owner@example.com',
            'password' => Hash::make('password'),
            'contact_number' => '09123456789',
            'business_name' => 'Test Parking Business',
            'business_address' => '123 Test Street, Test City',
            'payment_details' => json_encode([
                'bank_name' => 'Test Bank',
                'account_number' => '1234567890',
                'account_name' => 'Test Owner'
            ]),
            'status' => 'active'
        ]);
    }
}
