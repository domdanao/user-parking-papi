<div class="p-4">
    <div class="max-w-7xl mx-auto bg-white p-4 box">
		<div class="w-full flex items-center gap-3 mt-2">
			<div>
				<img src="{{ asset('/images/car-parking.png') }}" alt="Parking Papi" class="h-14 w-14">
			</div>
			<div class="flex flex-col divide-y divide-gray-200 w-full gap-1">
				<div class="text-sm font-bold text-gray-700 tracking-widest uppercase">Parking Slot #</div>
				<div class="font-mono font-extrabold text-2xl pt-1">{{ $slot->identifier }}</div>
			</div>
		</div>

		<!-- Google Map here, use data from database (lat, long) -->
		<div class="mt-6">
			<iframe
				width="100%"
				height="250"
				style="border:0"
				loading="lazy"
				allowfullscreen
				referrerpolicy="no-referrer-when-downgrade"
				src="https://www.google.com/maps/embed/v1/search?key={{ env('GOOGLE_MAPS_API_KEY') }}
					&q={{ $location['latitude'] }}+{{ $location['longitude'] }}
					&zoom=19
					&maptype=roadmap
					&language=en
					&region=PH">
			</iframe>
		</div>
		
		@if(!$slot->hasActiveRateCard())
			<div class="mt-4 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
				<p class="font-medium">This parking slot is currently unavailable</p>
				<p class="text-sm">No active rate card has been set up for this parking slot. Please contact the owner.</p>
			</div>
		@else
		<div class="text-xl mt-4 flex flex-col gap-1">
			<div class="flex flex-col gap-1 py-2">
				<div class="flex items-center gap-1 py-1">
					<div class="whitespace-nowrap font-medium text-gray-900 uppercase text-sm w-1/4">Plate #</div>
					<div class="whitespace-nowrap text-gray-700 w-3/4 flex flex-col gap-1">
						<input autofocus type="text" maxlength="8" wire:model.live="plate_no" class="border border-gray-300 rounded-md px-3 w-full py-2 shadow placeholder:text-gray-400 font-bold" placeholder="ABC1234">
						@error('plate_no') 
							<span class="text-red-600 text-xs font-medium">{{ $message }}</span>
						@enderror
					</div>
				</div>
				<div class="flex items-center gap-1">
					<div class="whitespace-nowrap font-medium text-gray-900 uppercase text-sm w-1/4"></div>
					<div class="text-gray-700 w-3/4">
						<p class="text-xs text-gray-500 font-semibold">No plate number? Use your conduction sticker number instead.</p>
					</div>
				</div>
			</div>

			<div class="flex items-center gap-1 py-2">
				<div class="whitespace-nowrap font-medium text-gray-900 uppercase text-sm w-1/4">Duration</div>
				<div class="whitespace-nowrap text-gray-700 w-3/4">
					<select wire:model.live="hours" class="border border-gray-300 rounded-md px-3 py-2 w-full shadow font-bold">
						<option value="2">2 Hours</option>
						<option value="3">3 Hours</option>
						<option value="4">4 Hours</option>
						<option value="5">5 Hours</option>
						<option value="6">6 Hours</option>
						<option value="7">7 Hours</option>
						<option value="8">8 Hours</option>
						<option value="9">9 Hours</option>
						<option value="10">10 Hours</option>
						<option value="11">11 Hours</option>
						<option value="12">12 Hours</option>
					</select>
				</div>
			</div>
		</div>

		<button type="button" class="flex justify-between w-full rounded-lg bg-blue-600 text-white mt-3 py-2 text-xl font-bold {{ $this->isPlateNumberValid() ? 'shimmer' : '' }}" wire:click="pay" wire:loading.attr="disabled" {{ !$slot->hasActiveRateCard() || !$this->isPlateNumberValid() ? 'disabled' : '' }}>
			<div></div>
			<div><span wire:target="pay">PAY&nbsp;</span>₱<span class="font-mono">₱{{ number_format($this->amount() / 100, 2) }}</span></div>
			<div>
				<span wire:loading wire:target="pay">
					<svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
						<circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"></circle>
						<path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
					</svg>
				</span>
			</div>

			
		</button>
		<!-- If there is convenience fee, show it -->
		@if (env('PARKING_CONVENIENCE_FEE') > 0)
			<div class="text-sm mt-1 mb-1 text-center font-semibold">+ Convenience Fee: ₱{{ number_format((int)env('PARKING_CONVENIENCE_FEE') / 100, 2) }}</div>
		@endif

		<div class="text-xs text-gray-700 font-normal text-center">By clicking pay, you agree to our <a href="#" class="text-blue-600">terms and conditions</a></div>
		
		@endif

		<div class="text-sm text-gray-700 font-semibold mt-8 mb-3 p-2 bg-gray-100 border border-dashed border-black text-center">This parking slot is owned by <b>{{ $slot->owner->name }}</b>. Rates may be subject to change by the owner.</div>

		<div class="flex justify-between items-center gap-2 border-t border-gray-500 border-dotted pt-2 mt-4">
			<div><img src="{{ asset('/images/parking-papi.jpg') }}" alt="Parking Papi" class="h-6 w-6 rounded-md"></div>
			<div><span class="text-xs uppercase whitespace-nowrap">Powered by</span>&nbsp;<span class="font-bold text-sm">Parking Papi</span></div>
			<div><span class="text-xs whitespace-nowrap">Copyright &copy; {{ date('Y') }}</div>
		</div>
			
    </div>
</div>
