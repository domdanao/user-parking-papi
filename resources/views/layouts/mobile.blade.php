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
    </head>
    <body class="font-sans antialiased min-h-screen flex flex-col bg-gray-100 dark:bg-gray-950">
		<main class="flex-grow bg-gray-100 dark:bg-gray-950">
			<div class="max-w-md mx-auto bg-gray-200 dark:bg-gray-900 min-h-screen">
				{{ $slot }}
			</div>
		</main>
    </body>
</html>

