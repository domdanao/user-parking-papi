<?php

namespace App\Http\Controllers;

use App\Models\ParkingSession;
use App\Services\ZipCheckoutService\ZipCheckoutService;
use Illuminate\Http\Request;

class ParkingPaymentController extends Controller
{
    public function success(Request $request, ZipCheckoutService $checkoutService)
    {
        $session = $checkoutService->getSession($request->query('session_id'));

        // Create parking session from metadata
        $parkingSession = ParkingSession::create([
            'slot_id' => $session->metadata['slot_id'],
            'plate_no' => $session->metadata['plate_no'],
            'duration' => $session->metadata['duration'],
            'amount_paid' => $session->amount_total,
            'payment_id' => $session->id,
            'payment_status' => $session->status,
            'starts_at' => now(),
            'ends_at' => now()->addSeconds($session->metadata['duration'] / 100), // Convert cents to seconds
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
