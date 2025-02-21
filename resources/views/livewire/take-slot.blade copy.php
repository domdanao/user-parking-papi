<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6 lg:p-8">
                <h1 class="text-2xl font-medium text-gray-900">
                    Slot {{ $slot->identifier }}
                </h1>

                <div class="mt-6">
                    <div class="bg-white overflow-hidden shadow rounded-lg">
                        <div class="p-5">
                            <dl>
                                <div class="px-4 py-2">
                                    <dt class="text-sm font-medium text-gray-500">Name</dt>
                                    <dd class="mt-1 text-lg text-gray-900">{{ $slot->name }}</dd>
                                </div>

                                <div class="px-4 py-2">
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd class="mt-1">
                                        <span @class([
                                            'px-2 py-1 text-xs font-medium rounded-full',
                                            'bg-green-100 text-green-700' => $slot->status === 'available',
                                            'bg-yellow-100 text-yellow-700' => $slot->status === 'occupied',
                                            'bg-red-100 text-red-700' => $slot->status === 'unavailable',
                                        ])>
                                            {{ ucfirst($slot->status) }}
                                        </span>
                                    </dd>
                                </div>

                                <div class="px-4 py-2">
                                    <dt class="text-sm font-medium text-gray-500">Location</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        @php
                                            $location = json_decode($slot->location, true) ?? [];
                                            $metadata = json_decode($slot->metadata, true) ?? [];
                                        @endphp
                                        Lat: {{ $location['latitude'] ?? 'N/A' }}, Long: {{ $location['longitude'] ?? 'N/A' }}
                                    </dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </div>

                @if($slot->status === 'available')
                    <div class="mt-6">
                        <button type="button" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Take This Slot
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
