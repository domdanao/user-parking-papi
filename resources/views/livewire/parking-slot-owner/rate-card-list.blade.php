<x-slot name="header">
    <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            @if($slot)
                {{ __('Rate Cards for') }} {{ $slot->name }}
            @else
                {{ __('Rate Card Templates') }}
            @endif
        </h2>
        @if($showCreateButton)
            <a href="{{ route('parking-slot-owner.rate-cards.create') }}" wire:navigate class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">
                {{ __('Create New Template') }}
            </a>
        @endif
    </div>
</x-slot>

<div>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">

        <!-- Status Messages -->
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="mb-4 font-medium text-sm text-red-600">
                {{ session('error') }}
            </div>
        @endif

        <!-- Warning Banner for Slots Needing Updates -->
        @if($slotsNeedingUpdate->isNotEmpty())
            <div class="mb-6 rounded-md bg-yellow-50 p-4">
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
                            <p>{{ __('The following slots have rate cards that need to be updated:') }}</p>
                            <ul class="mt-2 list-disc list-inside space-y-1">
                                @foreach($slotsNeedingUpdate as $slot)
                                    <li>
                                        <a href="{{ route('parking-slot-owner.rate-cards.slots.index', $slot) }}" 
                                            wire:navigate 
                                            class="underline hover:text-yellow-800"
                                        >
                                            {{ $slot->name }}
                                        </a>
                                        <span class="text-yellow-600">
                                            ({{ $slot->getFormattedRate() }})
                                        </span>
                                        <span class="text-yellow-700">
                                            - {{ $slot->getRateCardUpdateMessage() }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            @if($slot && $slot->hasRateCard())
                <div class="mb-6 rounded-md p-4
                    @switch($slot->getRateCardStatus())
                        @case('active')
                            bg-green-50
                            @break
                        @case('inactive')
                            bg-red-50
                            @break
                        @default
                            bg-gray-50
                    @endswitch">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 
                                @switch($slot->getRateCardStatus())
                                    @case('active')
                                        text-green-400
                                        @break
                                    @case('inactive')
                                        text-red-400
                                        @break
                                    @default
                                        text-gray-400
                                @endswitch" 
                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                @switch($slot->getRateCardStatus())
                                    @case('active')
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        @break
                                    @case('inactive')
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                        @break
                                    @default
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                @endswitch
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium 
                                @switch($slot->getRateCardStatus())
                                    @case('active')
                                        text-green-800
                                        @break
                                    @case('inactive')
                                        text-red-800
                                        @break
                                    @default
                                        text-gray-800
                                @endswitch">
                                {{ __('Current Rate Card') }}
                            </h3>
                            <div class="mt-2 text-sm 
                                @switch($slot->getRateCardStatus())
                                    @case('active')
                                        text-green-700
                                        @break
                                    @case('inactive')
                                        text-red-700
                                        @break
                                    @default
                                        text-gray-700
                                @endswitch">
                                <p>
                                    <span class="font-semibold">{{ $slot->rateCard->name }}</span>
                                    <span class="ml-2">{{ $slot->getFormattedRate() }}</span>
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                                        @switch($slot->getRateCardStatus())
                                            @case('active')
                                                bg-green-100 text-green-800
                                                @break
                                            @case('inactive')
                                                bg-red-100 text-red-800
                                                @break
                                            @default
                                                bg-gray-100 text-gray-800
                                        @endswitch">
                                        {{ ucfirst($slot->getRateCardStatus()) }}
                                    </span>
                                    @if($slot->needsRateCardUpdate())
                                        <span class="ml-2 text-sm text-red-600">{{ $slot->getRateCardUpdateMessage() }}</span>
                                    @endif
                                </p>
                                @if($slot->rateCard->description)
                                    <p class="mt-1 text-sm">{{ $slot->rateCard->description }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($rateCards->isEmpty())
                <div class="p-6 text-center text-gray-500">
                    {{ __('No rate cards found. Create your first one!') }}
                </div>
            @else
                <!-- Info Banner -->
                @if($showTemplates)
                    <div class="mb-6 rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-blue-800">{{ __('Rate Card Templates') }}</h3>
                                <div class="mt-2 text-sm text-blue-700">
                                    <p>{{ __('Templates are reusable rate card configurations that can be assigned to multiple slots. When assigned, each slot gets its own copy of the rate card.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hour Block</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rate</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach ($rateCards as $rateCard)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ is_array($rateCard) ? $rateCard['name'] : $rateCard->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ is_array($rateCard) ? ($rateCard['description'] ?: '-') : ($rateCard->description ?: '-') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ is_array($rateCard) ? $rateCard['hour_block'] : $rateCard->hour_block }}
                                        {{ trans_choice('hour|hours', is_array($rateCard) ? $rateCard['hour_block'] : $rateCard->hour_block) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ is_array($rateCard) ? $rateCard['formatted_rate'] : $rateCard->getFormattedRate() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex flex-col gap-1">
                                            <div class="flex items-center gap-2">
                                                <button 
                                                    wire:click="toggleStatus({{ is_array($rateCard) ? $rateCard['id'] : $rateCard->id }})" 
                                                    class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        @if(is_array($rateCard) ? $rateCard['is_active'] : $rateCard->is_active) 
                                                            bg-green-100 text-green-800 hover:bg-green-200
                                                        @else 
                                                            bg-red-100 text-red-800 hover:bg-red-200
                                                        @endif
                                                        @if(is_array($rateCard) ? (!$rateCard['can_be_deactivated'] && $rateCard['is_active']) : (!$rateCard->canBeDeactivated() && $rateCard->is_active))
                                                            cursor-not-allowed opacity-75
                                                        @endif"
                                                    @if(is_array($rateCard) ? (!$rateCard['can_be_deactivated'] && $rateCard['is_active']) : (!$rateCard->canBeDeactivated() && $rateCard->is_active))
                                                        disabled
                                                        title="{{ __('Cannot deactivate template while it is being used by slots') }}"
                                                    @endif
                                                >
                                                    {{ is_array($rateCard) ? $rateCard['status_message'] : $rateCard->getStatusMessage() }}
                                                </button>

                                                @if(is_array($rateCard) ? $rateCard['is_template'] : $rateCard->is_template)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ __('Template') }}
                                                    </span>
                                                @endif
                                            </div>

                                            @if(is_array($rateCard) ? $rateCard['is_template'] : $rateCard->is_template)
                                                <div class="flex items-center gap-2">
                                                    @if(is_array($rateCard) ? $rateCard['usage_count'] > 0 : $rateCard->usage_count > 0)
                                                        <span class="inline-flex items-center gap-1 text-xs text-gray-600">
                                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" />
                                                            </svg>
                                                            {{ trans_choice(
                                                                'Used by :count slot|Used by :count slots',
                                                                is_array($rateCard) ? $rateCard['usage_count'] : $rateCard->usage_count,
                                                                ['count' => is_array($rateCard) ? $rateCard['usage_count'] : $rateCard->usage_count]
                                                            ) }}
                                                        </span>
                                                    @else
                                                        <span class="text-xs text-gray-500">{{ __('Not in use') }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div class="flex space-x-2">
                                            @if($slot)
                                                <button 
                                                    wire:click="assignTemplate({{ is_array($rateCard) ? $rateCard['id'] : $rateCard->id }})" 
                                                    wire:confirm="{{ is_array($rateCard) ? $rateCard['assignment_status']['message'] : $rateCard->getAssignmentStatus($slot)['message'] }}"
                                                    class="{{ is_array($rateCard) ? ($rateCard['assignment_status']['can_assign'] ? 'text-green-600 hover:text-green-900' : 'text-gray-400 cursor-not-allowed') : ($rateCard->getAssignmentStatus($slot)['can_assign'] ? 'text-green-600 hover:text-green-900' : 'text-gray-400 cursor-not-allowed') }}"
                                                    {{ is_array($rateCard) ? (!$rateCard['assignment_status']['can_assign'] ? 'disabled' : '') : (!$rateCard->getAssignmentStatus($slot)['can_assign'] ? 'disabled' : '') }}
                                                    title="{{ is_array($rateCard) ? $rateCard['assignment_status']['message'] : $rateCard->getAssignmentStatus($slot)['message'] }}"
                                                >
                                                    {{ $slot->hasRateCard() ? __('Replace') : __('Assign') }}
                                                </button>
                                            @endif
                                            <a href="{{ route('parking-slot-owner.rate-cards.edit', is_array($rateCard) ? $rateCard['id'] : $rateCard) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Edit') }}
                                            </a>
                                            <button 
                                                wire:click="deleteRateCard({{ is_array($rateCard) ? $rateCard['id'] : $rateCard->id }})" 
                                                wire:confirm="{{ is_array($rateCard) ? ($rateCard['can_be_deleted'] ? __('Are you sure you want to delete this template?') : __('Cannot delete template while it is being used by :count slots', ['count' => $rateCard['usage_count']])) : $rateCard->getDeletionWarning() }}"
                                                class="{{ is_array($rateCard) ? ($rateCard['can_be_deleted'] ? 'text-red-600 hover:text-red-900' : 'text-gray-400 cursor-not-allowed') : ($rateCard->canBeDeleted() ? 'text-red-600 hover:text-red-900' : 'text-gray-400 cursor-not-allowed') }}"
                                                {{ is_array($rateCard) ? (!$rateCard['can_be_deleted'] ? 'disabled' : '') : (!$rateCard->canBeDeleted() ? 'disabled' : '') }}
                                                title="{{ is_array($rateCard) ? (!$rateCard['can_be_deleted'] ? __('Cannot delete template while it is being used by :count slots', ['count' => $rateCard['usage_count']]) : '') : (!$rateCard->canBeDeleted() ? __('Cannot delete template while it is being used by :count slots', ['count' => $rateCard->usage_count]) : '') }}"
                                            >
                                                {{ __('Delete') }}
                                            </button>
                                        </div>
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
