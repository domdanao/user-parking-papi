<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Parking Papi') }}</title>
		<!-- Favicon -->
		<link rel="icon" href="{{ asset('/images/favicon.png') }}" type="image/png">
        <!-- Fonts -->
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Figtree:ital,wght@0,300..900;1,300..900&family=JetBrains+Mono:ital,wght@0,100..800;1,100..800&display=swap" rel="stylesheet">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
		<script>
			function getContrastingTextColor(element) {
				const bgColor = window.getComputedStyle(element).backgroundColor;
				const rgb = bgColor.match(/\d+/g);
				const r = parseInt(rgb[0]);
				const g = parseInt(rgb[1]);
				const b = parseInt(rgb[2]);
				
				const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
				
				element.classList.remove('text-gray-800', 'text-gray-900', 'text-white');
				const textColor = luminance > 0.5 ? 'text-gray-900' : 'text-white';
				element.classList.add(textColor);
				
				element.querySelectorAll('p, span, div').forEach(textElement => {
					textElement.classList.remove('text-gray-800', 'text-gray-900', 'text-white');
					textElement.classList.add(textColor);
				});
			}
	
			// Run as soon as DOM is ready
			document.addEventListener('DOMContentLoaded', function() {
				document.querySelectorAll('.auto-contrast').forEach(getContrastingTextColor);
			});
	
			// Also run immediately for elements that already exist
			if (document.readyState !== 'loading') {
				document.querySelectorAll('.auto-contrast').forEach(getContrastingTextColor);
			}
		</script>
		<style>
			.box {
  				--mask: 
    				conic-gradient(from 135deg at top,#0000,#000 1deg 89deg,#0000 90deg) top/7px 51% repeat-x,
    				conic-gradient(from -45deg at bottom,#0000,#000 1deg 89deg,#0000 90deg) bottom/7px 51% repeat-x;
  				-webkit-mask: var(--mask);
          			mask: var(--mask);
			}
		</style>
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col bg-gray-100 dark:bg-gray-950">
		<!-- Header -->
		@if (isset($header))
		<header class="sticky top-0 z-50 bg-gray-100 dark:bg-gray-950">
			<div class="max-w-md mx-auto dark:bg-gray-900">
				{{ $header }}
			</div>
		</header>
		@endif

		<!-- Page Content -->
		<main class="flex-grow bg-gray-100 dark:bg-gray-950">
			<div class="max-w-md mx-auto bg-gray-200 dark:bg-gray-900 min-h-screen">
				{{ $slot }}
			</div>
		</main>

		<!-- Sticky Footer -->
		@if (isset($footer))
		<footer class="sticky bottom-0 z-50">
			<div class="max-w-md mx-auto">
				{{ $footer }}
			</div>
		</footer>
		@endif
    </body>
</html>

