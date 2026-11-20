<?php

use Illuminate\Support\Facades\Storage;

// NOTE: `setting()` comes from outerweb/settings (vendor autoload runs first, so a
// definition here would never take effect). It resolves a key *and* its dotted children,
// which is how repeater/array values are stored.

if (! function_exists('ml_setting')) {
    function ml_setting(string $field, $default = null, ?string $locale = null)
    {
        $currentLocale = $locale ?: (app()->getLocale() ?: 'de');

        $value = setting($currentLocale.'.'.$field);

        // German is the reference language: anything not translated yet shows in German.
        if (! $value && $currentLocale !== 'de') {
            $value = setting('de.'.$field);
        }

        return $value ?: $default;
    }
}

if (! function_exists('img_url')) {
    /**
     * Uploaded image from the admin, or the bundled design image when none was uploaded.
     */
    function img_url(?string $path, string $fallback): string
    {
        return $path ? Storage::disk('public')->url($path) : asset('images/'.$fallback);
    }
}

if (! function_exists('site_locales')) {
    /**
     * Languages switched on in the admin. German is always on.
     */
    function site_locales(): array
    {
        return array_values(array_filter(
            config('app.supported_locales'),
            fn (string $locale) => $locale === 'de' || setting('site.locale_'.$locale),
        ));
    }
}

if (! function_exists('video_embed')) {
    /**
     * The stories video chosen in the admin, as ['iframe' | 'mp4', src], or null.
     */
    function video_embed(): ?array
    {
        $url = trim((string) ml_setting('stories.video_url'));

        return match (ml_setting('stories.video_type')) {
            'youtube' => preg_match('~(?:youtu\.be/|[?&]v=|/embed/|/shorts/|/live/)([\w-]{11})~', $url, $m)
                ? ['iframe', "https://www.youtube-nocookie.com/embed/{$m[1]}?autoplay=1&rel=0"]
                : null,
            'drive' => preg_match('~(?:/d/|[?&]id=)([\w-]{20,})~', $url, $m)
                ? ['iframe', "https://drive.google.com/file/d/{$m[1]}/preview"]
                : null,
            'upload' => ($file = ml_setting('stories.video')) ? ['mp4', Storage::disk('public')->url($file)] : null,
            default => null,
        };
    }
}

if (! function_exists('contact_subjects')) {
    /**
     * Options of the contact form "Anliegen" select (one per line in the admin).
     */
    function contact_subjects(): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', (string) ml_setting('contact.subjects')))));
    }
}

if (! function_exists('raisenow_url')) {
    /**
     * RaiseNow form URL in the visitor's language, or null when the form must stay hidden:
     * it is a preview (admins only) until the foundation switches it live.
     */
    function raisenow_url(): ?string
    {
        $url = trim((string) setting('donation.raisenow_url'));

        if ($url === '' || (! setting('donation.live') && ! auth()->check())) {
            return null;
        }

        $url = rtrim(preg_replace('/([?&])lng=[^&]*&?/', '$1', $url), '?&');

        return $url.(str_contains($url, '?') ? '&' : '?').'lng='.app()->getLocale();
    }
}
