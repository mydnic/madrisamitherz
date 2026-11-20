@extends('layouts.main')

@php
    $email = ml_setting('links.contact_email', 'stiftung@madrisa.ch');
    $video = video_embed();
    $raisenow = raisenow_url();
    $icon = fn (?string $name) => asset('images/icons/'.(array_key_exists((string) $name, App\Support\SiteContent::ICONS) ? $name : 'website').'.svg');
    // Decorative polaroids around the donation form (desktop only): [file, left % of 1280, top px, width px]
    $polaroids = [
        ['polaroid-1.webp', -2, 349, 352],
        ['polaroid-3.webp', -4.6, 73, 332],
        ['polaroid-5.webp', -5, 612, 356],
        ['polaroid-7.webp', -3.5, 848, 402],
        ['polaroid-2.webp', 79.4, 63, 405],
        ['polaroid-4.webp', 76.7, 366, 313],
        ['polaroid-6.webp', 79.6, 596, 363],
        ['polaroid-8.webp', 74.2, 846, 387],
    ];
@endphp

@section('content')
    @include('partials.header')

    <main id="top">
        {{-- Hero --}}
        <section class="relative overflow-hidden bg-brown bg-[url(/images/wood.webp)] bg-cover bg-center text-white">
            <div class="container-x flex flex-col gap-7 py-10 lg:min-h-[766px] lg:flex-row lg:items-center lg:gap-0 lg:py-0 xl:pl-[119px]">
                <div class="order-2 lg:order-1 lg:max-w-[537px]">
                    <h1 class="font-ui text-[34px] leading-[1.1] font-bold tracking-[-0.02em] lg:font-sans lg:text-[45px] lg:leading-[1.2] lg:tracking-normal">{{ ml_setting('hero.title') }}</h1>
                    <p class="mt-5 font-ui text-lg leading-normal lg:mt-4 lg:max-w-[480px] lg:font-sans lg:text-xl lg:leading-[1.2]">{{ ml_setting('hero.text') }}</p>
                    <div class="mt-5 flex flex-wrap gap-3 lg:mt-[70px] lg:gap-[23px]">
                        <a href="#spenden" class="btn btn-primary lg:w-[186px]">
                            <img src="{{ asset('images/heart.svg') }}" alt="" width="17" height="15">
                            {{ ml_setting('hero.cta_primary') }}
                        </a>
                        <a href="#wirkung" class="btn bg-cream-btn text-brown lg:w-[177px]">{{ ml_setting('hero.cta_secondary') }}</a>
                    </div>
                </div>
                <img src="{{ img_url(ml_setting('hero.image'), 'hero-heart.webp') }}" alt="" width="630" height="544" class="order-1 mx-auto w-full max-w-[630px] lg:order-2 lg:-mt-[60px] lg:-mr-[66px] lg:ml-auto">
            </div>
        </section>

        {{-- What a donation does --}}
        <section class="relative isolate overflow-hidden bg-[#f7f8fa] lg:bg-white">
            <img src="{{ asset('images/mountain-bg.webp') }}" alt="" class="absolute inset-0 -z-10 hidden size-full object-cover opacity-10 lg:block">
            <div class="container-x grid gap-6 py-16 lg:grid-cols-[1fr_495px] lg:gap-0 lg:pt-[109px] lg:pb-[130px]">
                <div>
                    <p class="eyebrow">{{ ml_setting('gift.eyebrow') }}</p>
                    <h2 class="heading mt-2.5 lg:mt-1 lg:max-w-[589px]">{{ ml_setting('gift.title') }}</h2>
                    <img src="{{ img_url(ml_setting('gift.image'), 'polaroid-gift.webp') }}" alt="" width="350" height="333" loading="lazy" class="mx-auto mt-6 w-full max-w-[350px] lg:hidden">
                    <div class="mt-6 grid gap-3.5 sm:grid-cols-2 lg:mt-[54px] lg:max-w-[550px] lg:gap-2.5">
                        @foreach (ml_setting('gift.tiers', []) as $tier)
                            <div class="card flex flex-col gap-3 px-6 py-5 lg:gap-2 lg:rounded-[12.5px] lg:px-5 lg:py-[17px]">
                                <p class="font-ui text-[32px] leading-tight font-bold text-orange lg:font-sans lg:text-[26px]">{{ $tier['amount'] }}</p>
                                @foreach (['winter', 'summer'] as $season)
                                    <div>
                                        <p class="font-ui text-xs font-bold tracking-[0.08em] text-taupe lg:text-[8.5px]">{{ ml_setting("gift.{$season}_label") }}</p>
                                        <p class="mt-0.5 font-ui text-base leading-snug text-brown lg:font-sans lg:text-xs">{{ $tier[$season] }}</p>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    </div>
                </div>
                <img src="{{ img_url(ml_setting('gift.image'), 'polaroid-gift.webp') }}" alt="" width="495" height="522" loading="lazy" class="hidden w-full lg:block">
            </div>
        </section>

        {{-- Donation form (RaiseNow) --}}
        <section id="spenden" class="relative z-10 bg-brown bg-[url(/images/wood.webp)] bg-cover bg-center pt-16 pb-16 text-white lg:min-h-[1083px] lg:pt-[70px] lg:pb-[120px]">
            @foreach ($polaroids as [$image, $left, $top, $width])
                <img src="{{ asset('images/'.$image) }}" alt="" loading="lazy" class="pointer-events-none absolute hidden h-auto xl:block" style="left: {{ $left }}%; top: {{ $top }}px; width: {{ $width }}px">
            @endforeach

            <div class="container-x relative text-center">
                <p class="eyebrow text-white">{{ ml_setting('donation.eyebrow') }}</p>
                <h2 class="heading mx-auto mt-2.5 max-w-[589px] text-white lg:mt-1">{{ ml_setting('donation.title') }}</h2>

                <div class="mx-auto mt-6 max-w-[620px] rounded-[20px] bg-white p-5 text-left text-brown lg:mt-[78px] lg:rounded-[17px] lg:p-6">
                    @if ($raisenow)
                        <div id="rnw-embed" class="min-h-[400px]"></div>
                        <script type="module">
                            import { SolutionEmbed } from 'https://cdn.jsdelivr.net/npm/@raisenow/solution-embed@1/dist/index.js';
                            SolutionEmbed.render('#rnw-embed', { url: @js($raisenow), info: false });
                        </script>
                        @unless (setting('donation.live'))
                            <p class="mt-3 text-center font-ui text-xs text-orange">{{ ml_setting('donation.preview_notice') }}</p>
                        @endunless
                    @else
                        <p class="font-ui text-base leading-relaxed">{{ ml_setting('donation.unavailable_text') }}</p>
                        <a href="#kontakt" class="btn btn-primary mt-6 w-full">{{ ml_setting('header.nav.contact') }}</a>
                    @endif
                </div>
            </div>
        </section>

        {{-- Current activities --}}
        <section id="aktuell" class="container-x py-16 lg:pt-[178px] lg:pb-[150px]">
            <p class="eyebrow">{{ ml_setting('activities.eyebrow') }}</p>
            <h2 class="heading mt-2.5 lg:mt-0">{{ ml_setting('activities.title') }}</h2>
            <p class="lead mt-2.5 lg:mt-1 lg:max-w-[463px]">{{ ml_setting('activities.text') }}</p>
            <div class="mt-8 grid gap-9 md:grid-cols-3 lg:mt-[13px] lg:gap-[36px]">
                @foreach (ml_setting('activities.items', []) as $item)
                    <div>
                        <img src="{{ img_url($item['image'] ?? null, 'activity-website.webp') }}" alt="" loading="lazy" class="photo aspect-[350/240] w-full lg:aspect-[303/298]">
                        <span class="icon-badge mt-3.5 lg:mt-[22px]"><img src="{{ $icon($item['icon'] ?? null) }}" alt="" class="size-6 lg:size-[17px]"></span>
                        <p class="mt-3.5 font-ui text-[17px] leading-normal text-brown lg:mt-2.5 lg:font-sans lg:text-base lg:leading-[1.35]">{{ $item['text'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- Impact in numbers --}}
        <section id="wirkung" class="container-x pb-12 lg:pb-[153px]">
            <div class="grid gap-2.5 lg:grid-cols-[323px_317px] lg:gap-0">
                <div>
                    <p class="eyebrow">{{ ml_setting('impact.eyebrow') }}</p>
                    <h2 class="heading mt-2.5 lg:mt-0 lg:max-w-[278px]">{{ ml_setting('impact.title') }}</h2>
                </div>
                <p class="lead lg:pt-11">{{ ml_setting('impact.text') }}</p>
            </div>
            <dl class="mt-6 grid grid-cols-2 gap-3 md:grid-cols-4 lg:mt-[100px] lg:max-w-[1028px]">
                @foreach (ml_setting('impact.stats', []) as $stat)
                    <div class="card flex flex-col justify-center px-6 py-5 lg:min-h-[136px] lg:rounded-[12.5px] lg:px-8 lg:py-4">
                        <dt class="order-2 mt-1.5 font-ui text-xs leading-tight font-bold tracking-[0.04em] text-brown uppercase lg:text-sm lg:tracking-normal">{{ $stat['label'] }}</dt>
                        <dd class="font-ui text-[34px] leading-tight font-bold text-orange lg:text-[57px] lg:tracking-[-0.04em]">{{ $stat['value'] }}</dd>
                    </div>
                @endforeach
            </dl>
        </section>

        {{-- Stories --}}
        <section class="bg-cream-btn py-16 lg:bg-cream lg:pt-[43px] lg:pb-[73px]">
            <div class="container-x">
                <p class="eyebrow">{{ ml_setting('stories.eyebrow') }}</p>
                <h2 class="heading mt-2.5 lg:mt-0">{{ ml_setting('stories.title') }}</h2>
            </div>

            <div x-data="{ playing: false }" class="relative mx-5 mt-6 aspect-[350/220] overflow-hidden rounded-[18px] bg-orange sm:mx-10 lg:mx-0 lg:mt-[26px] lg:aspect-[1280/572] lg:rounded-none">
                <img x-show="!playing" src="{{ img_url(ml_setting('stories.poster'), 'video-bg.webp') }}" alt="" loading="lazy" class="size-full object-cover mix-blend-multiply">
                @if ($video)
                    {{-- Player is only created on click: no YouTube/Drive request before the visitor wants it --}}
                    <template x-if="playing">
                        @if ($video[0] === 'mp4')
                            <video src="{{ $video[1] }}" controls autoplay class="absolute inset-0 size-full bg-black object-contain"></video>
                        @else
                            <iframe src="{{ $video[1] }}" title="{{ ml_setting('stories.title') }}" allow="autoplay; encrypted-media; fullscreen; picture-in-picture" allowfullscreen class="absolute inset-0 size-full bg-black"></iframe>
                        @endif
                    </template>
                    <button x-show="!playing" type="button" @click="playing = true" class="absolute top-1/2 left-1/2 size-16 -translate-1/2 transition hover:scale-105 lg:size-[112px]" aria-label="Video abspielen">
                        <img src="{{ asset('images/play-circle.svg') }}" alt="" width="224" height="226" class="size-full">
                        <img src="{{ asset('images/play-triangle.svg') }}" alt="" width="97" height="130" class="absolute top-1/2 left-[56%] h-[58%] w-auto -translate-1/2">
                    </button>
                @endif
            </div>

            <div class="mx-auto mt-6 grid max-w-[1280px] gap-4 px-5 sm:px-10 md:grid-cols-2 lg:mt-[50px] lg:gap-[30px] lg:px-[44px]">
                @foreach (ml_setting('stories.testimonials', []) as $testimonial)
                    <figure class="card px-6 py-6 lg:rounded-[17.5px] lg:px-6 lg:pt-8 lg:pb-7">
                        <blockquote class="font-ui text-base leading-snug font-semibold text-black italic lg:text-[17.5px]">{{ $testimonial['quote'] }}</blockquote>
                        <figcaption class="mt-4 font-ui text-base text-orange lg:mt-6 lg:text-[17.5px]">{{ $testimonial['author'] }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </section>

        {{-- Projects --}}
        <section id="projekte" class="py-16 lg:pt-[124px] lg:pb-[56px]">
            <div class="container-x">
                <p class="eyebrow">{{ ml_setting('projects.eyebrow') }}</p>
                <h2 class="heading mt-2.5 lg:mt-0 lg:max-w-[443px]">{{ ml_setting('projects.title') }}</h2>
            </div>
            <ul class="mx-auto mt-6 grid max-w-[1280px] grid-cols-2 gap-x-3.5 gap-y-6 px-5 sm:px-10 md:grid-cols-4 lg:mt-[10px] lg:gap-x-[27px] lg:px-[119px]">
                @foreach (ml_setting('projects.items', []) as $project)
                    <li class="text-center">
                        <img src="{{ img_url($project['image'] ?? null, 'project-summerdream.webp') }}" alt="{{ $project['title'] }}" loading="lazy" class="photo aspect-[168/360] w-full lg:aspect-[245/365]">
                        <h3 class="mt-3.5 font-ui text-sm font-bold tracking-[0.06em] text-ink uppercase lg:mt-[18px] lg:text-[14.5px] lg:tracking-[0.04em] lg:text-brown">{{ $project['title'] }}</h3>
                    </li>
                @endforeach
            </ul>
            @if ($ctaUrl = ml_setting('projects.cta_url'))
                <div class="container-x mt-8 lg:mt-[28px] lg:text-center">
                    <a href="{{ $ctaUrl }}" target="_blank" rel="noopener" class="btn btn-outline h-[52px] border-orange-btn px-7 lg:h-[62px] lg:border-brown lg:px-[27px] lg:text-[20.5px]">{{ ml_setting('projects.cta_label') }}</a>
                </div>
            @endif
        </section>

        {{-- About the foundation --}}
        <section id="ueber-uns" class="bg-cream-btn py-16 lg:bg-cream lg:pt-[74px] lg:pb-[85px]">
            <div class="container-x">
                <div class="flex flex-col gap-6 lg:flex-row lg:justify-between">
                    <img src="{{ asset('images/logo-big.webp') }}" alt="Madrisa mit Herz Stiftung" width="232" height="220" loading="lazy" class="mx-auto w-[220px] lg:order-2 lg:mx-0 lg:w-[232px]">
                    <div class="lg:order-1">
                        <p class="eyebrow">{{ ml_setting('about.eyebrow') }}</p>
                        <h2 class="heading mt-2.5 lg:mt-0 lg:max-w-[589px]">{{ ml_setting('about.title') }}</h2>
                        <p class="lead mt-2.5 lg:mt-1.5 lg:max-w-[546px]">{{ ml_setting('about.text') }}</p>
                    </div>
                </div>
                <div class="mt-8 grid gap-9 sm:grid-cols-2 lg:mt-[10px] lg:grid-cols-4 lg:gap-5">
                    @foreach (ml_setting('about.purposes', []) as $purpose)
                        <div>
                            <img src="{{ img_url($purpose['image'] ?? null, 'purpose-infrastruktur.webp') }}" alt="" loading="lazy" class="photo aspect-[350/240] w-full lg:aspect-[237/235]">
                            <span class="icon-badge mt-3.5 lg:mt-[22px]"><img src="{{ $icon($purpose['icon'] ?? null) }}" alt="" class="size-6 lg:size-[17px]"></span>
                            <p class="mt-3.5 font-ui text-[17px] leading-normal text-brown lg:mt-2.5 lg:font-sans lg:text-base lg:leading-[1.35]">{{ $purpose['text'] }}</p>
                        </div>
                    @endforeach
                </div>
                <p class="mt-10 flex items-start gap-3 font-ui text-base font-bold text-brown lg:mt-[63px] lg:items-center lg:gap-2.5 lg:font-sans lg:text-lg">
                    <img src="{{ asset('images/icons/heart-orange.svg') }}" alt="" width="20" height="18" class="mt-0.5 shrink-0 lg:mt-0">
                    {{ ml_setting('about.nonprofit') }}
                </p>
            </div>
        </section>

        {{-- Board --}}
        <section class="container-x py-16 lg:pt-[151px] lg:pb-[72px]">
            <h2 class="heading lg:max-w-[589px]">{{ ml_setting('board.title') }}</h2>
            <div class="mt-6 grid gap-5 md:grid-cols-2 lg:mt-[81px] lg:gap-[15px]">
                @foreach (ml_setting('board.members', []) as $member)
                    <article class="card flex flex-col overflow-hidden lg:flex-row lg:rounded-[13px]">
                        <img src="{{ img_url($member['photo'] ?? null, 'person-placeholder.webp') }}" alt="{{ $member['name'] }}" loading="lazy" class="h-[230px] w-full object-cover lg:h-[190px] lg:w-[150px] lg:shrink-0">
                        <div class="flex flex-col gap-2 px-7 py-6 font-ui lg:gap-[7px] lg:px-6 lg:py-[22px]">
                            <h3 class="text-2xl leading-tight font-bold tracking-[-0.02em] text-ink lg:text-[23px] lg:tracking-[-0.03em]">{{ $member['name'] }}</h3>
                            <p class="text-xs font-bold tracking-[0.06em] text-orange uppercase lg:text-[11px]">{{ $member['role'] }}</p>
                            @if ($member['description'] ?? null)
                                <p class="text-base leading-snug text-brown lg:text-[13.5px]">{{ $member['description'] }}</p>
                            @endif
                            @if ($member['statement'] ?? null)
                                <p class="text-base leading-snug font-bold text-orange lg:text-sm">{{ $member['statement'] }}</p>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>

        {{-- Network --}}
        <section class="container-x pb-16 lg:pt-[73px] lg:pb-[45px]">
            <p class="eyebrow">{{ ml_setting('network.eyebrow') }}</p>
            <h2 class="heading mt-2.5 lg:mt-0">{{ ml_setting('network.title') }}</h2>
            <div class="mt-6 grid gap-6 lg:mt-[32px] lg:grid-cols-[380px_1fr] lg:gap-[42px]">
                @if ($networkImage = ml_setting('network.image'))
                    <img src="{{ Storage::disk('public')->url($networkImage) }}" alt="{{ ml_setting('network.image_title') }}" loading="lazy" class="photo aspect-[350/440] w-full lg:aspect-[380/505]">
                @else
                    <div class="flex aspect-[350/440] flex-col items-center justify-center gap-2.5 rounded-[18px] bg-sand px-6 text-center font-ui shadow-[0_4px_13px_-6px_rgba(0,0,0,0.11)] lg:aspect-[380/505] lg:rounded-[13px]">
                        <img src="{{ asset('images/icons/image.svg') }}" alt="" class="size-12">
                        <p class="text-lg font-bold text-brown lg:text-[17px]">{{ ml_setting('network.image_title') }}</p>
                        <p class="text-[13px] text-taupe lg:text-xs">{{ ml_setting('network.image_caption') }}</p>
                    </div>
                @endif
                <ul class="flex flex-col gap-3 lg:gap-5">
                    @foreach (ml_setting('network.partners', []) as $partner)
                        <li>
                            <a @if ($partner['url'] ?? null) href="{{ $partner['url'] }}" target="_blank" rel="noopener" @endif class="card flex items-center gap-5 px-4 py-3.5 font-ui lg:min-h-[85px] lg:gap-[18px] lg:rounded-[11px] lg:px-3 lg:py-3">
                                <span class="flex h-16 w-[120px] shrink-0 items-center justify-center rounded-[10px] bg-sand lg:h-[61px] lg:rounded-[7px]">
                                    @if ($partner['logo'] ?? null)
                                        <img src="{{ Storage::disk('public')->url($partner['logo']) }}" alt="{{ $partner['name'] }}" loading="lazy" class="max-h-[48px] max-w-[100px] object-contain">
                                    @else
                                        <span class="text-[13px] font-bold text-taupe lg:text-xs">Logo</span>
                                    @endif
                                </span>
                                <span class="flex flex-col gap-1">
                                    <span class="text-xl font-bold text-ink lg:text-lg">{{ $partner['name'] }}</span>
                                    @if ($partner['role'] ?? null)
                                        <span class="text-[15px] leading-snug text-brown lg:text-[13px]">{{ $partner['role'] }}</span>
                                    @endif
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        {{-- Stronger together --}}
        <section class="relative isolate overflow-hidden pt-16 pb-[200px] sm:pb-[300px] lg:pt-[66px] lg:pb-[380px]">
            <img src="{{ asset('images/group-photo.webp') }}" alt="" loading="lazy" class="absolute inset-x-0 bottom-0 -z-10 h-[260px] w-full object-cover object-bottom opacity-30 sm:h-[340px] lg:inset-0 lg:h-full lg:object-[center_85%]">
            <div class="container-x">
                <p class="eyebrow">{{ ml_setting('together.eyebrow') }}</p>
                <h2 class="heading mt-2.5 lg:mt-0 lg:max-w-[526px]">{{ ml_setting('together.title') }}</h2>
                <p class="lead mt-6 lg:mt-1.5 lg:max-w-[546px]">{{ ml_setting('together.text') }}</p>
                <div class="mt-6 flex flex-wrap gap-3 lg:mt-[10px] lg:gap-[27px]">
                    <a href="#spenden" class="btn btn-primary lg:w-[167px]">
                        <img src="{{ asset('images/heart.svg') }}" alt="" width="17" height="15">
                        {{ ml_setting('together.cta_primary') }}
                    </a>
                    <a href="#kontakt" class="btn btn-outline lg:w-[177px]">{{ ml_setting('together.cta_secondary') }}</a>
                </div>
            </div>
        </section>

        {{-- FAQ --}}
        <section id="faq" class="bg-cream-btn py-16 lg:py-[100px]">
            <div class="container-x flex flex-col gap-6 lg:flex-row lg:gap-[60px]">
                <h2 class="heading lg:w-[150px] lg:shrink-0">{{ ml_setting('faq.title') }}</h2>
                <div class="flex-1 lg:max-w-[805px]">
                    @foreach (ml_setting('faq.items', []) as $faq)
                        <details class="group border-b border-line py-5 lg:py-[22px]" @if ($loop->first) open @endif>
                            <summary class="flex cursor-pointer list-none items-center justify-between gap-5 lg:gap-5 [&::-webkit-details-marker]:hidden">
                                <span class="text-[17px] leading-tight font-bold tracking-[-0.02em] text-ink lg:text-2xl">{{ $faq['question'] }}</span>
                                <img src="{{ asset('images/icons/plus.svg') }}" alt="" class="size-8 shrink-0 group-open:hidden lg:size-9">
                                <img src="{{ asset('images/icons/minus.svg') }}" alt="" class="hidden size-8 shrink-0 group-open:block lg:size-9">
                            </summary>
                            <p class="mt-2.5 font-ui text-base leading-normal text-brown lg:pr-[70px] lg:font-sans lg:text-[19px] lg:leading-normal">{!! nl2br(e($faq['answer'])) !!}</p>
                        </details>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- Contact --}}
        <section id="kontakt" class="py-16 lg:py-[100px]">
            <div class="container-x flex flex-col gap-6 lg:flex-row lg:gap-[60px]">
                <div class="lg:w-[320px] lg:shrink-0">
                    <p class="eyebrow lg:font-ui lg:tracking-[0.04em]">{{ ml_setting('contact.eyebrow') }}</p>
                    <h2 class="heading mt-2.5 lg:mt-3 lg:leading-[1.05]">{{ ml_setting('contact.title') }}</h2>
                    <p class="lead mt-2.5 lg:mt-3 lg:text-[19px] lg:leading-normal">{{ ml_setting('contact.text') }}</p>
                </div>

                <div class="flex-1 rounded-[20px] bg-cream-btn p-5 lg:max-w-[635px] lg:rounded-[17px] lg:p-9">
                    @if (session('contact_sent'))
                        <p class="rounded-[10px] bg-white p-5 font-ui text-base text-brown" role="status">{{ ml_setting('contact.success') }}</p>
                    @else
                        <form method="POST" action="{{ route('contact.send') }}" class="grid gap-5 font-ui sm:grid-cols-2 sm:gap-x-4">
                            @csrf
                            <input type="text" name="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">

                            @foreach (['name' => ['text', 'name'], 'email' => ['email', 'email'], 'phone' => ['tel', 'tel']] as $field => [$type, $autocomplete])
                                <label class="flex flex-col gap-[7px]">
                                    <span class="text-[15px] font-bold text-brown">{{ ml_setting("contact.{$field}_label") }}</span>
                                    <input type="{{ $type }}" name="{{ $field }}" value="{{ old($field) }}" autocomplete="{{ $autocomplete }}" placeholder="{{ ml_setting("contact.{$field}_placeholder") }}" @required($field !== 'phone') class="field">
                                    @error($field, 'contact') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                                </label>
                            @endforeach

                            <label class="flex flex-col gap-[7px]">
                                <span class="text-[15px] font-bold text-brown">{{ ml_setting('contact.subject_label') }}</span>
                                <select name="subject" class="field appearance-none bg-[url(/images/icons/chevron-down.svg)] bg-[length:18px] bg-[right_16px_center] bg-no-repeat pr-10">
                                    <option value="">{{ ml_setting('contact.subject_placeholder') }}</option>
                                    @foreach (contact_subjects() as $subject)
                                        <option value="{{ $subject }}" @selected(old('subject') === $subject)>{{ $subject }}</option>
                                    @endforeach
                                </select>
                                @error('subject', 'contact') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>

                            <label class="flex flex-col gap-[7px] sm:col-span-2">
                                <span class="text-[15px] font-bold text-brown">{{ ml_setting('contact.message_label') }}</span>
                                <textarea name="message" rows="5" required placeholder="{{ ml_setting('contact.message_placeholder') }}" class="field h-[150px] resize-y py-3">{{ old('message') }}</textarea>
                                @error('message', 'contact') <span class="text-sm text-red-700">{{ $message }}</span> @enderror
                            </label>

                            <label class="flex items-start gap-2.5 sm:col-span-2">
                                <input type="checkbox" name="consent" value="1" required @checked(old('consent')) class="mt-0.5 size-5 shrink-0 rounded accent-orange-btn">
                                <span class="text-[15px] leading-snug text-brown">{{ ml_setting('contact.consent') }}</span>
                            </label>
                            @error('consent', 'contact') <span class="text-sm text-red-700 sm:col-span-2">{{ $message }}</span> @enderror

                            <div class="sm:col-span-2">
                                <button type="submit" class="btn btn-primary w-full sm:w-[211px]">{{ ml_setting('contact.submit') }}</button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </section>
    </main>

    @include('partials.footer')
@endsection
