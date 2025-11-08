<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
    <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
        <!-- Logo centralizada acima do formulário -->
        <a href="/" class="mb-8 flex justify-center">
            <img class="img-fluid" src="{{ asset('img/feira-on-sem-fundo.png') }}" style="width: 140px;">
        </a>

        <!-- Formulário -->
        <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
    </body>


</html>
