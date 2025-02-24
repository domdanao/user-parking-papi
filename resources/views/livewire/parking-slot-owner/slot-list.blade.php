<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <x-slot name="header">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        {{ __('My Parking Slots') }}
                    </h2>
                    <a href="{{ route('parking-slot-owner.rate-cards.index') }}" wire:navigate class="text-sm text-indigo-600 hover:text-indigo-900">
                        {{ __('Manage Rate Card Templates') }}
                    </a>
                </div>
                <a href="{{ route('parking-slot-owner.slots.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                    {{ __('Create New Slot') }}
                </a>
            </div>
        </x-slot>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            @if ($slots->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    {{ __('No parking slots found. Create your first one!') }}
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Identifier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate Card</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($slots as $slot)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $slot->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $slot->identifier }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            @if($slot->status === 'available') bg-green-100 text-green-800
                                            @elseif($slot->status === 'occupied') bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800
                                            @endif">
                                            {{ ucfirst($slot->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($slot->location)
                                            {{ $slot->location['latitude'] }}, {{ $slot->location['longitude'] }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($slot->rateCard)
                                            <span class="text-gray-900">{{ $slot->rateCard->name }}</span><br>
                                            <span class="text-sm">₱{{ number_format($slot->rateCard->rate, 2) }} / {{ $slot->rateCard->hour_block }}hr</span>
                                        @else
                                            <span class="text-red-600">{{ __('No rate card assigned') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $slot->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a href="{{ route('parking-slot-owner.rate-cards.slots.index', $slot) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('Manage Rates') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
