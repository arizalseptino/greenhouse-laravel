<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Greenhouse Monitoring') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-gray-100">

<div class="min-h-screen grid grid-cols-1 md:grid-cols-2">

    <!-- KIRI: FORM LOGIN / REGISTER -->
    <div class="flex items-center justify-center px-6">
        <div class="w-full max-w-md bg-white shadow-md rounded-lg px-6 py-6">
            {{ $slot }}
        </div>
    </div>

    <!-- KANAN: FOTO GREENHOUSE -->
    <div class="hidden md:block relative">
        <img
            src="{{ asset('assets/img/bayam.jpg') }}"
            alt="Greenhouse"
            class="absolute inset-0 w-full h-full object-cover"
        >

        <!-- Overlay -->
        <div class="absolute inset-0 bg-emerald-900/40"></div>

        <!-- Text -->
        <div class="absolute bottom-10 left-10 text-white">
            <h2 class="text-3xl font-bold">Greenhouse Monitoring</h2>
            <p class="text-sm opacity-90">
                Sistem Monitoring & Kontrol Iklim Tanaman
            </p>
        </div>
    </div>

</div>

</body>
</html>
