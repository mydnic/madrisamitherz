@php
    $home = url('/');
    $nav = [
        '#top' => ml_setting('header.nav.home'),
        '#projekte' => ml_setting('header.nav.projects'),
        '#aktuell' => ml_setting('header.nav.support'),
        '#ueber-uns' => ml_setting('header.nav.about'),
        '#spenden' => ml_setting('header.nav.donate'),
    ];
    $locales = site_locales();
@endphp

<header x-data="{ open: false }" class="sticky top-0 z-40 bg-brown text-white">
    <div class="flex h-[69px] items-center gap-4 px-5 lg:h-[88px] lg:px-[63px]">
        <a href="{{ $home }}#top" class="shrink-0" aria-label="Stiftung Madrisa mit Herz">
            <img src="{{ asset('images/logo-header.svg') }}" alt="Madrisa mit Herz Stiftung" width="172" height="163" class="h-12 w-auto lg:h-[82px]">
        </a>

        <nav class="mx-auto hidden items-center gap-[26px] font-ui text-[14.5px] lg:flex" aria-label="Hauptnavigation">
            @foreach ($nav as $anchor => $label)
                <a href="{{ $home.$anchor }}" class="hover:text-orange-btn">{{ $label }}</a>
            @endforeach
            <a href="{{ $home }}#spenden" class="btn btn-primary ml-[14px] w-[143px] px-0">{{ ml_setting('header.cta') }}</a>
        </nav>

        @if (count($locales) > 1)
            <div class="ml-auto flex gap-2 font-ui text-xs uppercase lg:ml-0">
                @foreach ($locales as $locale)
                    <a href="{{ route('language.switch', $locale) }}" @class(['opacity-60 hover:opacity-100' => $locale !== app()->getLocale()])>{{ $locale }}</a>
                @endforeach
            </div>
        @endif

        <div @class(['flex items-center gap-4 lg:hidden', 'ml-auto' => count($locales) < 2])>
            <a href="{{ $home }}#spenden" class="btn btn-primary h-[49px] px-7">
                <img src="{{ asset('images/heart.svg') }}" alt="" width="18" height="16">
                {{ ml_setting('header.cta_short') }}
            </a>
            <button type="button" @click="open = !open" :aria-expanded="open" aria-controls="mobile-nav" class="p-1" aria-label="Menü">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true">
                    <path x-show="!open" d="M4 7h16M4 12h16M4 17h16" />
                    <path x-show="open" x-cloak d="M6 6l12 12M18 6L6 18" />
                </svg>
            </button>
        </div>
    </div>

    <nav id="mobile-nav" x-show="open" x-cloak @click="open = false" class="flex flex-col gap-4 border-t border-white/10 px-5 py-6 font-ui text-lg lg:hidden" aria-label="Mobile Navigation">
        @foreach ($nav as $anchor => $label)
            <a href="{{ $home.$anchor }}">{{ $label }}</a>
        @endforeach
        <a href="{{ $home }}#faq">{{ ml_setting('header.nav.faq') }}</a>
        <a href="{{ $home }}#kontakt">{{ ml_setting('header.nav.contact') }}</a>
    </nav>
</header>
