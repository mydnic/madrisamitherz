<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ ml_setting('meta.title', 'Stiftung Madrisa mit Herz') }}</title>
    <meta name="description" content="{{ ml_setting('meta.description') }}">
    <meta property="og:title" content="{{ ml_setting('meta.title') }}">
    <meta property="og:description" content="{{ ml_setting('meta.description') }}">
    <meta property="og:image" content="{{ asset('images/hero-heart.webp') }}">

    <link rel="icon" href="/favicon.ico" sizes="48x48">
    <link rel="icon" type="image/png" href="{{ img_url(ml_setting('meta.favicon_image'), 'favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fira+Sans:ital,wght@0,400;0,500;0,700;1,600&family=Inter:ital,wght@0,400;0,600;0,700;1,600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-white font-sans text-ink antialiased">
    @yield('content')
</body>

</html>
