<?php

namespace App\Livewire\ParkingSlotOwner;

use App\Models\Slot;
use App\Models\ParkingSlotOwner;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SlotList extends Component
{
    #[Layout('layouts.parking-slot-owner')]
    public function render()
    {
        if (!auth('parking-slot-owner')->check()) {
            return redirect()->route('parking-slot-owner.login');
        }

        /** @var ParkingSlotOwner $owner */
        $owner = Auth::guard('parking-slot-owner')->user();

        try {
            $slots = Slot::where('parking_slot_owner_id', $owner->id)
                ->with(['rateCard' => function ($query) {
                    $query->select('id', 'name', 'hour_block', 'rate', 'is_active');
                }])
                ->latest()
                ->get();

            Log::info('Fetched slots with rate cards', [
                'user_id' => $owner->id,
                'slot_count' => $slots->count(),
                'slots_with_rate_cards' => $slots->filter->rateCard->count()
            ]);

            return view('livewire.parking-slot-owner.slot-list', [
                'slots' => $slots
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch slots', [
                'error' => $e->getMessage(),
                'user_id' => $owner->id
            ]);

            session()->flash('error', __('Failed to load parking slots. Please try again.'));
            return view('livewire.parking-slot-owner.slot-list', [
                'slots' => collect()
            ]);
        }
    }
}
