<?php

namespace App\Policies;

use App\Models\ParkingSlotOwner;
use App\Models\Slot;

class SlotPolicy
{
    /**
     * Determine if the parking slot owner can update the slot.
     */
    public function update(ParkingSlotOwner $owner, Slot $slot): bool
    {
        return $owner->id === $slot->parking_slot_owner_id;
    }

    /**
     * Determine if the parking slot owner can delete the slot.
     */
    public function delete(ParkingSlotOwner $owner, Slot $slot): bool
    {
        return $owner->id === $slot->parking_slot_owner_id;
    }
}
