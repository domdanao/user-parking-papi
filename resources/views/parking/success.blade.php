@extends('layouts.minimal')

@section('content')
<div class="min-h-screen bg-gray-100 py-6 flex flex-col justify-center sm:py-12">
    <div class="relative py-3 sm:max-w-xl sm:mx-auto">
        <div class="relative px-4 py-10 bg-white shadow-lg sm:rounded-3xl sm:p-20">
            <div class="max-w-md mx-auto">
                <div class="divide-y divide-gray-200">
                    <div class="py-8 text-base leading-6 space-y-4 text-gray-700 sm:text-lg sm:leading-7">
                        <div class="flex items-center justify-center mb-8">
                            <svg class="h-16 w-16 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <h2 class="text-2xl font-bold text-center mb-8">Payment Successful!</h2>
                        <div class="bg-gray-50 p-6 rounded-lg">
                            <div class="mb-4">
                                <span class="font-semibold">Plate Number:</span>
                                <span>{{ $parkingSession->plate_no }}</span>
                            </div>
                            <div class="mb-4">
                                <span class="font-semibold">Duration:</span>
                                <span>{{ $parkingSession->duration / 100 }} seconds</span>
                            </div>
                            <div class="mb-4">
                                <span class="font-semibold">Amount Paid:</span>
                                <span>₱{{ number_format($parkingSession->amount_paid / 100, 2) }}</span>
                            </div>
                            <div class="mb-4">
                                <span class="font-semibold">Start Time:</span>
                                <span>{{ $parkingSession->starts_at->format('M d, Y h:i A') }}</span>
                            </div>
                            <div>
                                <span class="font-semibold">End Time:</span>
                                <span>{{ $parkingSession->ends_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                        <div class="mt-8 text-center">
                            <a href="{{ url('/') }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600">
                                Back to Home
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
