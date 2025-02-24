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
    public int $hours = 2; // Default to 2 hours
    public string $plate_no = '';

    public function mount($identifier)
    {
        $this->slot = Slot::with(['owner', 'rateCard'])->where('identifier', $identifier)->firstOrFail();
    }

    protected function calculateAmount(): int
    {
        if (!$this->slot->hasActiveRateCard()) {
            return 0; // Return 0 if no active rate card exists
        }

        // Base rate per hour from rate card
        $hourlyRate = $this->slot->getCurrentRate();
        
        // Progressive pricing logic
        $amount = match($this->hours) {
            2 => $hourlyRate * 2,
            3 => ($hourlyRate * 2) + ($hourlyRate + 1000),
            4 => ($hourlyRate * 2) + ($hourlyRate + 2000),
            5 => ($hourlyRate * 2) + ($hourlyRate + 3000),
            6 => ($hourlyRate * 2) + ($hourlyRate + 4000),
            7 => ($hourlyRate * 2) + ($hourlyRate + 5000),
            8 => ($hourlyRate * 2) + ($hourlyRate + 6000),
            9 => ($hourlyRate * 2) + ($hourlyRate + 7000),
            10 => ($hourlyRate * 2) + ($hourlyRate + 8000),
            11 => ($hourlyRate * 2) + ($hourlyRate + 9000),
            12 => ($hourlyRate * 2) + ($hourlyRate + 10000),
            default => $hourlyRate * $this->hours
        };

        return (int) $amount;
    }

    #[Computed]
    public function amount(): int
    {
        return $this->calculateAmount();
    }

    public function updatedHours($value)
    {
        $this->hours = (int) $value;
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

        $amount = $this->amount();
        $convenienceFee = config('parking.convenience_fee', 1500);

        // Create a checkout session
        $payload = [
            'currency' => 'PHP',
            'payment_method_types' => ['card', 'gcash', 'maya'],
            'success_url' => url("/parking/success"),
            'cancel_url' => url("/parking/cancel"),
            'client_reference_id' => $this->slot->identifier,
            'description' => "Parking at {$this->slot->name} - {$this->plate_no}",
            'line_items' => [
				[
					'name' => "Parking at {$this->slot->name}",
					'amount' => $amount,
					'currency' => 'PHP',
					'quantity' => 1,
					'description' => "{$this->hours} hours parking for {$this->plate_no}",
					'metadata' => [
						'slot_id' => (string) $this->slot->id,
						'plate_no' => (string) $this->plate_no,
						'hours' => (string) $this->hours,
					]
				],
			],
            'metadata' => [
                'slot_id'  => (string) $this->slot->id,
                'plate_no' => (string) $this->plate_no,
                'hours' => (string) $this->hours,
            ],
            'mode' => 'payment',
            'submit_type' => 'pay',
        ];

        // Add convenience fee if configured
        if ($convenienceFee > 0) {
            $payload['line_items'][] = [
                'name' => 'Convenience Fee',
                'amount' => $convenienceFee,
                'currency' => 'PHP',
                'quantity' => 1,
                'description' => 'Convenience fee'
            ];
            $payload['amount'] = $amount + $convenienceFee;
        } else {
            $payload['amount'] = $amount;
        }

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
