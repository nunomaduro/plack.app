<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: #faf8f5;
            }

            html.dark {
                background-color: #14110c;
            }
        </style>

        @php
            $appName = config('app.name', 'Plack');
            $appDescription = 'Plack is a fast, open-source team chat app. Spin up a workspace, organize conversations into channels, and keep your whole team in sync — a fresh, focused alternative to Slack.';
            $appUrl = url()->current();
            $appImage = url('/og-image.png');
        @endphp

        <title inertia>{{ $appName }}</title>

        <meta name="description" content="{{ $appDescription }}">
        <meta name="author" content="{{ $appName }}">
        <meta name="theme-color" content="#e5a23d" media="(prefers-color-scheme: light)">
        <meta name="theme-color" content="#0c0a06" media="(prefers-color-scheme: dark)">
        <link rel="canonical" href="{{ $appUrl }}">

        {{-- Open Graph / Facebook --}}
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="{{ $appName }}">
        <meta property="og:title" content="{{ $appName }} — team chat that keeps you in focus">
        <meta property="og:description" content="{{ $appDescription }}">
        <meta property="og:url" content="{{ $appUrl }}">
        <meta property="og:image" content="{{ $appImage }}">
        <meta property="og:image:width" content="1966">
        <meta property="og:image:height" content="987">
        <meta property="og:image:alt" content="{{ $appName }} — a Slack-style team chat app built with Laravel and React">

        {{-- Twitter / X --}}
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $appName }} — team chat that keeps you in focus">
        <meta name="twitter:description" content="{{ $appDescription }}">
        <meta name="twitter:image" content="{{ $appImage }}">
        <meta name="twitter:image:alt" content="{{ $appName }} — a Slack-style team chat app built with Laravel and React">

        <link rel="icon" type="image/png" href="/favicon/favicon-96x96.png" sizes="96x96">
        <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
        <link rel="shortcut icon" href="/favicon/favicon.ico">
        <link rel="apple-touch-icon" sizes="180x180" href="/favicon/apple-touch-icon.png">
        <meta name="apple-mobile-web-app-title" content="{{ $appName }}">
        <link rel="manifest" href="/favicon/site.webmanifest">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        @viteReactRefresh
        @fonts
        @vite(['resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
