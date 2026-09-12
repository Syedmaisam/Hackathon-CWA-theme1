<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    {{-- viewport-fit=cover is what makes env(safe-area-inset-*) resolve to a real
         value on a notched iPhone. Without it the safe-area padding on the chat
         composer and the tab bar silently computes to zero. --}}
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title inertia>{{ config('app.name', 'The City Around You') }}</title>

    {{-- Installed PWA: tints the phone status bar and the splash screen teal so a
         home-screen launch never flashes white browser chrome. --}}
    <meta name="theme-color" content="#0d9488">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="City Around You">

    <link rel="manifest" href="/manifest.webmanifest">

    {{-- iOS ignores the manifest icons entirely and reads only apple-touch-icon. --}}
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="icon" href="/favicon.ico" sizes="32x32">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;700&display=swap" rel="stylesheet">

    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    @inertiaHead
</head>
<body class="bg-stone-50 text-stone-900 antialiased">
    @inertia

    @production
        {{-- Registered by hand rather than by the plugin: the worker sits at the
             document root so its scope covers every page, and no worker is built
             in dev, where Vite serves assets from memory. --}}
        <script>
            if ('serviceWorker' in navigator) {
                window.addEventListener('load', () => {
                    navigator.serviceWorker.register('/sw.js', { scope: '/' });
                });
            }
        </script>
    @endproduction
</body>
</html>
