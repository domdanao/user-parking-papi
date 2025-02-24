<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                @if($slot)
                    {{ __('Create Rate Card for') }} {{ $slot->name }}
                @else
                    {{ __('Create Rate Card Template') }}
                @endif
            </h2>
        </x-slot>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <form wire:submit="save" class="p-6 space-y-6">
                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Rate Name')" />
                    <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-500">{{ __('E.g. "Weekday Rate", "Weekend Rate", "Holiday Rate"') }}</p>
                </div>

                <!-- Description -->
                <div>
                    <x-input-label for="description" :value="__('Description (Optional)')" />
                    <textarea wire:model="description" id="description" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full" rows="3"></textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>

                <!-- Hour Block -->
                <div>
                    <x-input-label for="hour_block" :value="__('Hour Block')" />
                    <x-text-input wire:model="hour_block" id="hour_block" class="block mt-1 w-full" type="number" min="1" required />
                    <x-input-error :messages="$errors->get('hour_block')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-500">{{ __('Number of hours this rate applies to') }}</p>
                </div>

                <!-- Rate -->
                <div>
                    <x-input-label for="rate" :value="__('Rate Amount (₱)')" />
                    <x-text-input wire:model="rate" id="rate" class="block mt-1 w-full" type="number" min="1" required />
                    <x-input-error :messages="$errors->get('rate')" class="mt-2" />
                    <p class="mt-1 text-sm text-gray-500">{{ __('Amount to charge for this hour block') }}</p>
                </div>

                <!-- Template Status (only show when creating without a slot) -->
                @if(!$slot)
                    <div class="flex items-center">
                        <input wire:model="is_template" id="is_template" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" checked disabled>
                        <label for="is_template" class="ml-2 block text-sm text-gray-900">
                            {{ __('This is a template') }}
                        </label>
                        <x-input-error :messages="$errors->get('is_template')" class="mt-2" />
                    </div>
                @endif

                <!-- Active Status -->
                <div class="flex items-center">
                    <input wire:model="is_active" id="is_active" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                    <label for="is_active" class="ml-2 block text-sm text-gray-900">
                        {{ __('Active') }}
                    </label>
                    <x-input-error :messages="$errors->get('is_active')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-4">
                    <a href="{{ $slot ? route('parking-slot-owner.rate-cards.index', $slot) : route('parking-slot-owner.rate-cards.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Cancel') }}
                    </a>
                    <x-primary-button class="ml-4">
                        {{ $slot ? __('Create Rate Card') : __('Create Template') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</div>
