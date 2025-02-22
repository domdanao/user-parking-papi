<div class="p-4 ">
    <div class="max-w-7xl mx-auto bg-white p-4 box">
		<div class="w-full flex items-center gap-3 mt-2">
			<div>
				<img src="{{ asset('/images/car-parking.png') }}" alt="Parking Papi" class="h-12">
			</div>
			<div class="flex flex-col">
				<div class="text-sm font-bold text-gray-700 tracking-widest uppercase">Parking Slot #</div>
				<div class="font-mono font-bold text-3xl">{{ $slot->identifier }}</div>
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
		<div class="text-xl mt-4 flex flex-col gap-1">
			<div class="flex flex-col gap-1 py-2">
				<div class="flex items-center gap-1 py-1">
					<div class="whitespace-nowrap font-medium text-gray-900 uppercase text-sm w-1/4">Plate #</div>
					<div class="whitespace-nowrap text-gray-700 w-3/4">
						<input autofocus type="text" wire:model="plate_no" class="border border-gray-300 rounded-md px-3 w-full py-2 shadow placeholder:text-gray-400 font-bold" placeholder="ABC 1234">
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
					<select wire:model.live="duration" class="border border-gray-300 rounded-md px-3 py-2 w-full shadow font-bold">
						<option value="6000">2 hours (₱60)</option>
						<option value="10000">3 hours (₱100)</option>
						<option value="15000">4 hours (₱150)</option>
						<option value="21000">5 hours (₱210)</option>
						<option value="28000">6 hours (₱280)</option>
						<option value="36000">7 hours (₱360)</option>
						<option value="45000">8 hours (₱450)</option>
						<option value="55000">9 hours (₱550)</option>
						<option value="65000">10 hours (₱650)</option>
						<option value="75000">11 hours (₱750)</option>
						<option value="85000">12 hours (₱850)</option>
					</select>
				</div>
			</div>
		</div>
		<button type="button" class="w-full rounded-lg bg-blue-600 text-white mt-3 text-center py-2 text-xl font-bold">PAY ₱{{ number_format($duration / 100, 2) }}</button>
		<div class="text-xs text-gray-700 font-normal mt-2 text-center">By clicking pay, you agree to our <a href="#" class="text-blue-600">terms and conditions</a></div>
		
		<div class="text-sm text-gray-700 font-semibold mt-8 mb-3 p-2 bg-gray-100 border border-dashed border-black text-center">This parking slot is owned by <b>{{ $slot->owner->name }}</b>. Rates may be subject to change by the owner.</div>
    </div>
</div>
