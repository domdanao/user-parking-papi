<x-mobile-layout>
	<div class="flex flex-col items-center">
		<h2 class="text-xl font-bold mb-2">Scan Parking Slot QR</h2>
		<video id="qr-video" style="width: 100%; max-width: 400px;" ></video>
		<p id="result">Scanning...</p>
		@vite(['resources/js/qrScanner.js'])
	</div>
</x-mobile-layout>
