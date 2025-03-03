<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        <x-slot name="header">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create New Parking Slot') }}
            </h2>
        </x-slot>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <form wire:submit="save" class="p-6">
                <div class="space-y-6">
                    <!-- Name -->
                    <div>
                        <x-input-label for="name" :value="__('Slot Name')" />
                        <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Identifier -->
                    <div>
                        <x-input-label for="identifier" :value="__('Identifier (Optional)')" />
                        <x-text-input wire:model="identifier" id="identifier" class="block mt-1 w-full" type="text" placeholder="Leave empty to auto-generate" />
                        <x-input-error :messages="$errors->get('identifier')" class="mt-2" />
                    </div>

                    <!-- Location -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="latitude" :value="__('Latitude')" />
                            <x-text-input wire:model="latitude" id="latitude" class="block mt-1 w-full" type="number" step="any" required />
                            <x-input-error :messages="$errors->get('latitude')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="longitude" :value="__('Longitude')" />
                            <x-text-input wire:model="longitude" id="longitude" class="block mt-1 w-full" type="number" step="any" required />
                            <x-input-error :messages="$errors->get('longitude')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Rate Card Template -->
                    <div>
                        <x-input-label for="rateCardTemplateId" :value="__('Rate Card Template (Optional)')" />
                        <select wire:model="rateCardTemplateId" id="rateCardTemplateId" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Select a rate card template</option>
                            @foreach($rateCardTemplates as $template)
                                <option value="{{ $template->id }}">{{ $template->name }} ({{ money($template->rate) }}/{{ $template->hour_block }}hr)</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('rateCardTemplateId')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Create Slot') }}
                        </x-primary-button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
