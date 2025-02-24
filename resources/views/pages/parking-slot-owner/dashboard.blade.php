<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 lg:p-8">
                    <h1 class="text-2xl font-medium text-gray-900">
                        Welcome, {{ Auth::guard('parking-slot-owner')->user()->name }}!
                    </h1>

                    <!-- Warning Banner for Slots Needing Updates -->
                    @if($slotsNeedingUpdate->isNotEmpty())
                        <div class="mt-6 rounded-md bg-yellow-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        {{ trans_choice(
                                            ':count slot has an inactive rate card|:count slots have inactive rate cards',
                                            $slotsNeedingUpdate->count(),
                                            ['count' => $slotsNeedingUpdate->count()]
                                        ) }}
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>{{ __('The following slots need their rate cards updated:') }}</p>
                                        <ul class="mt-2 list-disc list-inside space-y-1">
                                            @foreach($slotsNeedingUpdate as $slot)
                                                <li>
                                                    <a href="{{ route('parking-slot-owner.rate-cards.slots.index', $slot) }}" 
                                                        wire:navigate
                                                        class="underline hover:text-yellow-800 hover:underline"
                                                    >
                                                        {{ $slot->name }}
                                                    </a>
                                                    <span class="text-yellow-600">
                                                        ({{ $slot->getFormattedRate() }})
                                                    </span>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                            <!-- Slots Stats Card -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                                            </svg>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">Total Slots</dt>
                                                <dd class="flex flex-col gap-1">
                                                    <div class="text-2xl font-semibold text-gray-900">
                                                        {{ $totalSlots }}
                                                    </div>
                                                    @if($slotsNeedingUpdate->isNotEmpty())
                                                        <div class="flex items-center gap-2">
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                {{ trans_choice(':count needs update|:count need update', $slotsNeedingUpdate->count(), ['count' => $slotsNeedingUpdate->count()]) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-5 py-3">
                                    <div class="text-sm">
                                        <a href="{{ route('parking-slot-owner.slots.index') }}" wire:navigate class="font-medium text-indigo-700 hover:text-indigo-900">
                                            {{ __('Manage slots') }} &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!-- Rate Cards Stats Card -->
                            <div class="bg-white overflow-hidden shadow rounded-lg">
                                <div class="p-5">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <div class="ml-5 w-0 flex-1">
                                            <dl>
                                                <dt class="text-sm font-medium text-gray-500 truncate">Rate Card Templates</dt>
                                                <dd class="flex flex-col gap-1">
                                                    <div class="text-2xl font-semibold text-gray-900">
                                                        {{ $totalTemplates }}
                                                    </div>
                                                    <div class="flex items-center gap-2">
                                                        <span class="text-sm text-gray-600">
                                                            {{ $activeTemplates }} {{ __('active') }}
                                                        </span>
                                                        @if($slotsNeedingUpdate->isNotEmpty())
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                                                                {{ trans_choice(':count slot needs update|:count slots need update', $slotsNeedingUpdate->count(), ['count' => $slotsNeedingUpdate->count()]) }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </dd>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-5 py-3">
                                    <div class="text-sm">
                                        <a href="{{ route('parking-slot-owner.rate-cards.index') }}" wire:navigate class="font-medium text-indigo-700 hover:text-indigo-900">
                                            Manage rate templates &rarr;
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
