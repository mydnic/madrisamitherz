@php
    $home = url('/');
    $links = [
        '#top' => ml_setting('header.nav.home'),
        '#projekte' => ml_setting('header.nav.projects'),
        '#aktuell' => ml_setting('header.nav.support'),
        '#ueber-uns' => ml_setting('header.nav.about'),
        '#spenden' => ml_setting('header.nav.donate'),
        '#faq' => ml_setting('header.nav.faq'),
        '#kontakt' => ml_setting('header.nav.contact'),
    ];
@endphp

<footer class="bg-brown px-5 pt-10 pb-8 font-ui text-white lg:px-[132px] lg:pt-[50px] lg:pb-[35px]">
    <div class="mx-auto flex max-w-[1015px] flex-col items-center gap-7">
        <a href="{{ $home }}#top" aria-label="Stiftung Madrisa mit Herz">
            <img src="{{ asset('images/logo-footer.svg') }}" alt="Madrisa mit Herz Stiftung" width="211" height="199" loading="lazy" class="h-[76px] w-auto lg:h-[100px]">
        </a>
        <nav class="flex flex-wrap justify-center gap-x-6 gap-y-2 text-sm lg:gap-x-9 lg:text-[14.5px]" aria-label="Footer">
            @foreach ($links as $anchor => $label)
                <a href="{{ $home.$anchor }}" class="hover:text-orange-btn">{{ $label }}</a>
            @endforeach
        </nav>
        <div class="flex w-full flex-col items-center gap-2 border-t border-white/20 pt-7 text-xs text-white/70 lg:flex-row lg:justify-between">
            <p>{{ str_replace(':year', date('Y'), ml_setting('footer.copyright')) }}</p>
            <div class="flex gap-6">
                <a href="{{ route('impressum') }}" class="hover:text-white">{{ ml_setting('footer.impressum') }}</a>
                <a href="{{ route('datenschutz') }}" class="hover:text-white">{{ ml_setting('footer.datenschutz') }}</a>
            </div>
        </div>
    </div>
</footer>
