<?php

namespace App\Http\Controllers;

use App\Models\ParkingSession;
use App\Services\ZipCheckoutService\ZipCheckoutService;
use Illuminate\Http\Request;

class ParkingPaymentController extends Controller
{
    public function success(Request $request, ZipCheckoutService $checkoutService)
    {
		// Get session from ZipCheckout
        $session = $checkoutService->getSession($request->query('session_id'));

		// Check if session is paid
		if (!$session->payment_status === 'paid') {
            return redirect()->route('parking.cancel');
        }

        // Create parking session from metadata
        $parkingSession = ParkingSession::create([
            'slot_id' => $session->metadata['slot_id'],
            'plate_number' => $session->metadata['plate_no'],
            'start_time' => now(),
            'end_time' => now()->addHours($session->metadata['hours']),
            'status' => 'active',
            'total_amount' => $session->amount_total,
            'duration_hours' => $session->metadata['hours'],
            'convenience_fee' => config('parking.convenience_fee', 1500)
        ]);

        return view('parking.success', [
            'parkingSession' => $parkingSession
        ]);
    }

    public function cancel()
    {
        return view('parking.cancel');
    }
}
