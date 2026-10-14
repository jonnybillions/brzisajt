@props([
    'title' => tx('site.meta.title'),
    'description' => tx('site.meta.description'),
    'canonical' => url()->current(),
])
<!doctype html>
<html lang="sr-Latn" class="no-js">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta name="theme-color" content="#121016">
    <meta name="format-detection" content="telephone=no">
    <link rel="icon" href="{{ asset('favicon.svg') }}" type="image/svg+xml">
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="32x32">

    <meta property="og:type" content="website">
    <meta property="og:locale" content="sr_RS">
    <meta property="og:site_name" content="{{ __('site.brand.name') }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ asset('og.png') }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta name="twitter:card" content="summary_large_image">

    {{-- Fontovi za prvi ekran — bez čekanja na CSS --}}
    <link rel="preload" href="{{ Vite::asset('node_modules/@fontsource-variable/unbounded/files/unbounded-latin-wght-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/unbounded-sr-wght.woff2') }}" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('head')
    @include('partials.analytics')
</head>
<body class="min-h-dvh antialiased">
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:top-3 focus:left-3 focus:z-[100] btn btn-lime">{{ __('site.a11y.skip') }}</a>

    @include('partials.header')

    <main id="main" tabindex="-1" class="outline-none">
        {{ $slot }}
    </main>

    @include('partials.footer')

    @include('partials.consent')

    @livewireScriptConfig
    @stack('scripts')
</body>
</html>
