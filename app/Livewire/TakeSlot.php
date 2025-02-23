<?php

namespace App\Livewire;

use App\Models\Slot;
use App\Models\ParkingSlotOwner;
use App\Services\ZipCheckoutService\ZipCheckoutService;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

class TakeSlot extends Component
{
    public Slot $slot;
    public $duration = 6000; // Default to 2 hours (₱60.00)
    public $plate_no = '';

    public function mount($identifier)
    {
        $this->slot = Slot::with('owner')->where('identifier', $identifier)->firstOrFail();
    }

    public function updatedDuration($value)
    {
        $this->duration = (int) $value;
    }

    public function isPlateNumberValid()
    {
        return strlen(trim($this->plate_no)) >= 6 && strlen(trim($this->plate_no)) <= 8;
    }

    public function pay(ZipCheckoutService $checkoutService)
    {
        // Validate required fields
        $this->validate([
            'plate_no' => 'required|min:6|max:8',
        ]);

        // Create a checkout session
		$payload = [
			'currency' => 'PHP',
            'payment_method_types' => ['card', 'gcash', 'maya'],
            'success_url' => url("/parking/success"),
            'cancel_url' => url("/parking/cancel"),
            'client_reference_id' => $this->slot->identifier,
            'description' => "Parking at {$this->slot->name} - {$this->plate_no}",
            'line_items' => [[
                'name' => "Parking at {$this->slot->name}",
                'amount' => $this->duration,
                'currency' => 'PHP',
                'quantity' => 1,
                'description' => "Vehicle plate number: {$this->plate_no}",
                'metadata' => [
                    'slot_id' => (string) $this->slot->id,
                    'plate_no' => (string) $this->plate_no,
                    'duration' => (string) $this->duration,
                ]
            ]],
            'metadata' => [
                'slot_id'  => (string) $this->slot->id,
                'plate_no' => (string) $this->plate_no,
                'duration' => (string) $this->duration,
            ],
            'mode' => 'payment',
            'submit_type' => 'pay',
		];
        $session = $checkoutService->createSession($payload);

        // Redirect to Zip Checkout
        return redirect($session->payment_url);
    }

    #[Layout('layouts.minimal')]
    public function render()
    {
        if(gettype($this->slot->location) === 'string') {
            $location = json_decode($this->slot->location, true);
        } else {
            $location = $this->slot->location;
        }

        $owner = $this->slot->owner;
        
        return view('livewire.take-slot', [
            'slot' => $this->slot,
            'location' => $location,
            'owner' => $owner
        ]);
    }
}
