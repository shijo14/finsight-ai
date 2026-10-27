<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <x-theme-init />

        <title>{{ config('app.name', 'FinSight AI') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,700|figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen bg-neutral-50 dark:bg-finsight-gradient">
        <div class="flex flex-col items-center justify-center py-12 min-h-screen px-4">
            <div class="mb-6 flex w-full max-w-md items-center justify-between">
                <a href="/" class="text-2xl font-bold tracking-tight text-neutral-900 dark:text-white">
                    FinSight <span class="text-cyan-600 dark:text-cyan-400">AI</span>
                </a>
                <x-theme-toggle compact />
            </div>

            <div class="w-full sm:max-w-md glass-card p-8">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
