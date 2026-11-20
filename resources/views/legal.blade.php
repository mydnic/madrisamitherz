@extends('layouts.main')

@section('content')
    @include('partials.header')

    <main class="bg-cream-btn py-16 lg:py-[100px]">
        {{-- Rich text written by admins in Filament --}}
        <article class="legal container-x max-w-[900px] font-ui text-brown">
            {!! str(ml_setting($field))->sanitizeHtml() !!}
        </article>
    </main>

    @include('partials.footer')
@endsection
