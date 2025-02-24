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
        $this->slot = Slot::with(['owner', 'rateCards'])->where('identifier', $identifier)->firstOrFail();
    }

    protected function calculateAmount(): int
    {
        // Get the current rate card from eager loaded relationship
        $rateCard = $this->slot->rateCards->sortByDesc('created_at')->first();
        
        if (!$rateCard) {
            return 0; // Return 0 if no rate card exists
        }

        // Base rate per hour from rate card
        $hourlyRate = $rateCard->amount;
        
        // Progressive pricing logic
        $amount = match($this->hours) {
            2 => $hourlyRate * 2,      // ₱60 (₱30/hr)
            3 => $hourlyRate * 3.33,   // ₱100 (₱33.33/hr)
            4 => $hourlyRate * 3.75,   // ₱150 (₱37.50/hr)
            5 => $hourlyRate * 4.2,    // ₱210 (₱42/hr)
            6 => $hourlyRate * 4.67,   // ₱280 (₱46.67/hr)
            7 => $hourlyRate * 5.14,   // ₱360 (₱51.43/hr)
            8 => $hourlyRate * 5.625,  // ₱450 (₱56.25/hr)
            9 => $hourlyRate * 6.11,   // ₱550 (₱61.11/hr)
            10 => $hourlyRate * 6.5,   // ₱650 (₱65/hr)
            11 => $hourlyRate * 6.82,  // ₱750 (₱68.18/hr)
            12 => $hourlyRate * 7.08,  // ₱850 (₱70.83/hr)
            default => $hourlyRate * $this->hours
        };

        return (int) ($amount * 100); // Convert to cents
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
            'line_items' => [[
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
            ]],
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
                'description' => 'External payment processing fee'
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
