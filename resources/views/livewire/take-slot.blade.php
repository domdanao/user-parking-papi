<div class="p-4 ">
    <div class="max-w-7xl mx-auto bg-white p-4 box">
		<div class="w-full flex items-center gap-2">
			<div>
				<img src="{{ asset('/images/parking-papi.jpg') }}" alt="Parking Papi" class="h-24 rounded-xl">
			</div>
			<div class="text-4xl font-bold">
				Welcome to<br>Parking Papi
			</div>
		</div>
		<div class="text-2xl font-bold mt-4">
			<!-- Table, with horizontal lines per row, -->
			<div class="overflow-x-auto">
				<table class="min-w-full text-lg divide-y divide-gray-600">
					<tbody class="divide-y divide-gray-600">
						<tr>
							<td class="whitespace-nowrap py-1 font-medium text-gray-900">
							</td>
						</tr>
						<tr>
							<td class="whitespace-nowrap py-4 font-medium text-gray-900">
								Slot name
							</td>
							<td class="whitespace-nowrap py-4 text-gray-700">{{ $slot->name }}</td>
						</tr>
						<tr>
							<td class="whitespace-nowrap py-4 font-medium text-gray-900">
								Slot ID
							</td>
							<td class="whitespace-nowrap py-4 text-gray-700">{{ $slot->identifier }}</td>
						</tr>
						<tr>
							<td class="whitespace-nowrap py-4 font-medium text-gray-900">
								Your plate no.
							</td>
							<td class="whitespace-nowrap text-gray-700  py-4">
								<input autofocus type="text" wire:model="plate_no" class="border border-gray-300 rounded-md px-1 py-1 shadow placeholder:text-gray-400" placeholder="ABC 1234">
							</td>
						</tr>
						<tr>
							<td class="whitespace-nowrap py-1 font-medium text-gray-900">
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="w-full rounded-lg bg-blue-600 text-white mt-4 text-center py-2 cursor-pointer">Pay</div>
			<div>
				<div class="text-xs text-gray-700 font-normal mt-1">By clicking pay, you agree to our <a href="#" class="text-blue-600">terms and conditions</a></div>
			</div>
			<hr class="my-4">
			<div>
				<div class="text-xl font-bold">Rates</div>
				<div class="text-sm text-gray-700 font-semibold mt-1">- First 2 hours: <b>P60.00</b></div>
				<div class="text-sm text-gray-700 font-semibold mt-1">- 3rd hour: <b>+P40.00</b></div>
				<div class="text-sm text-gray-700 font-semibold mt-1">- 4th hour: <b>+P50.00</b></div>
				<div class="text-sm text-gray-700 font-semibold mt-1">- 5th hour: <b>+P60.00</b></div>
				<div class="text-sm text-gray-700 font-semibold mt-1">- 6th hour: <b>+P70.00</b></div>
				<div class="text-sm text-gray-700 font-semibold mt-1">- 7th hour: <b>+P80.00</b></div>
				<div class="text-sm text-gray-700 font-semibold mt-1">- 8th hour: <b>+P90.00</b></div>
				<div class="text-sm text-gray-700 font-semibold mt-1">- 9th hour onwards: <b>+100.00 per hour</b></div>
			</div>
    </div>
</div>
