<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Window Trip — Your Complete Travel Partner')</title>
    <meta name="description" content="@yield('meta_description', 'Window Trip — tourist visa processing, air tickets and tour packages for India, USA, Europe, Thailand, Singapore, Malaysia and China.')">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph --}}
    <meta property="og:type" content="website">
    <meta property="og:title" content="@yield('title', 'Window Trip — Your Complete Travel Partner')">
    <meta property="og:description" content="@yield('meta_description', 'Tourist visa processing, air tickets and tour packages.')">
    <meta property="og:url" content="{{ url()->current() }}">

    <link rel="icon" href="/brand/icon.svg" type="image/svg+xml">

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/js/public.js'])
    @stack('head')
</head>
<body class="font-sans text-brand-ink antialiased bg-white">
    @include('partials.header')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

    @stack('scripts')
</body>
</html>
