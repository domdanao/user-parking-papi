<?php

namespace App\Livewire\Pages\ParkingSlotOwner\Auth;

use App\Models\ParkingSlotOwner;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $contact_number = '';
    public string $business_name = '';
    public string $business_address = '';
    public array $payment_details = [
        'type' => 'bank',
        'account_name' => '',
        'account_number' => '',
        'bank_name' => ''
    ];

    public function rules()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:parking_slot_owners'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'contact_number' => ['required', 'string', 'max:255'],
            'business_name' => ['required', 'string', 'max:255'],
            'business_address' => ['required', 'string'],
            'payment_details.account_name' => ['required', 'string', 'max:255'],
            'payment_details.account_number' => ['required', 'string', 'max:255'],
            'payment_details.bank_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function register()
    {
        $validated = $this->validate();

        $parkingSlotOwner = ParkingSlotOwner::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'contact_number' => $validated['contact_number'],
            'business_name' => $validated['business_name'],
            'business_address' => $validated['business_address'],
            'payment_details' => $this->payment_details,
        ]);

        auth('parking-slot-owner')->login($parkingSlotOwner);

        return redirect()->intended(route('parking-slot-owner.dashboard'));
    }

    public function render()
    {
        return view('livewire.pages.parking-slot-owner.auth.register');
    }
}
