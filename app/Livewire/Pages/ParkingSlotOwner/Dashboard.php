<?php

namespace App\Livewire\Pages\ParkingSlotOwner;

use App\Models\ParkingSlotOwner;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Dashboard extends Component
{
    #[Computed]
    public function slotsNeedingUpdate()
    {
        if (!auth('parking-slot-owner')->check()) {
            return collect();
        }

        /** @var ParkingSlotOwner $owner */
        $owner = Auth::guard('parking-slot-owner')->user();

        try {
            $slots = $owner->getSlotsNeedingRateCardUpdate();

            if ($slots->isNotEmpty()) {
                Log::warning('Found slots with inactive rate cards on dashboard', [
                    'user_id' => $owner->id,
                    'slot_count' => $slots->count(),
                    'slot_ids' => $slots->pluck('id')->toArray()
                ]);
            }

            return $slots;
        } catch (\Exception $e) {
            Log::error('Failed to check for slots needing rate card updates on dashboard', [
                'error' => $e->getMessage(),
                'user_id' => $owner->id
            ]);

            return collect();
        }
    }

    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        /** @var ParkingSlotOwner $owner */
        $owner = Auth::guard('parking-slot-owner')->user();

        $stats = [
            'total_slots' => $owner->slots()->count(),
            'available_slots' => $owner->slots()->available()->count(),
            'occupied_slots' => $owner->slots()->occupied()->count(),
            'unavailable_slots' => $owner->slots()->unavailable()->count(),
        ];

        return view('livewire.pages.parking-slot-owner.dashboard', [
            'stats' => $stats,
            'totalTemplates' => $owner->rateCardTemplates()->count(),
            'activeTemplates' => $owner->assignableRateCardTemplates()->count(),
            'slotsNeedingUpdate' => $this->slotsNeedingUpdate
        ]);
    }
}
