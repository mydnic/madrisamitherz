<?php

namespace App\Support;

/**
 * Default copy for every editable setting on the site.
 *
 * Single source of truth for `settings:sync` and the seeder. Keys are stored per
 * locale (prefixed with `de.`, `fr.`, ...) by whoever consumes this list.
 *
 * Array values (repeaters) are flattened to leaf rows by the settings package,
 * so they must be written through its setter — never as a raw JSON row.
 *
 * Images point to `defaults/<file>` on the public disk; `settings:sync` copies
 * the bundled file from public/images there, so the admin shows it pre-filled.
 */
class SiteContent
{
    /** Icons available for activities and purposes (files in public/images/icons). */
    public const ICONS = [
        'restaurants' => 'Restaurant',
        'website' => 'Website',
        'clubs' => 'Clubs / Auge',
        'infrastruktur' => 'Infrastruktur / Rollstuhl',
        'benefiz' => 'Kalender / Anlass',
        'arbeit' => 'Arbeit',
        'sport' => 'Sport / Pokal',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        $member = fn (string $name, string $role, ?string $photo = null, string $description = 'Short description – to come', string $quote = '«Personal statement – to come»') => [
            'name' => $name, 'role' => $role, 'description' => $description, 'statement' => $quote, 'photo' => $photo,
        ];
        $partner = fn (string $name) => ['name' => $name, 'role' => 'One sentence about their role – to come', 'logo' => null, 'url' => null];

        return [
            // Meta & links
            'meta.title' => 'Stiftung Madrisa mit Herz',
            'meta.description' => 'Inklusion auf 1900 M – Stiftung Madrisa mit Herz ermöglicht Menschen mit Behinderungen einzigartige Momente bei Sport, Spiel und Arbeit auf der Madrisa.',
            'meta.favicon_image' => 'defaults/favicon.png',
            'links.contact_email' => 'stiftung@madrisa.ch',

            // Header & footer
            'header.nav.home' => 'Home',
            'header.nav.projects' => 'Projekte',
            'header.nav.support' => 'Unterstützung erhalten',
            'header.nav.about' => 'Über uns',
            'header.nav.donate' => 'Spenden',
            'header.nav.faq' => 'FAQ',
            'header.nav.contact' => 'Kontakt',
            'header.cta' => 'Jetzt spenden',
            'header.cta_short' => 'Spenden',
            'footer.copyright' => '© :year Stiftung Madrisa mit Herz',
            'footer.impressum' => 'Impressum',
            'footer.datenschutz' => 'Datenschutz',

            // Hero
            'hero.title' => 'Inklusion auf 1900 M – mit Infrastruktur, Sport- und Spielmöglichkeiten und Arbeit',
            'hero.text' => 'Mit deiner Unterstützung ermöglichst du Menschen mit Behinderungen einzigartige Momente bei Sport und Spiel oder bei der Arbeit im Restaurant in einer einzigartigen Umgebung und kompletter Barrierefreiheit.',
            'hero.cta_primary' => 'Jetzt spenden',
            'hero.cta_secondary' => 'Unsere Wirkung',
            'hero.image' => 'defaults/hero-heart.webp',

            // What a donation does
            'gift.eyebrow' => 'WAS BEWIRKT DEINE SPENDE?',
            'gift.title' => 'Spende einen glücklichen Moment',
            'gift.image' => 'defaults/polaroid-gift.webp',
            'gift.winter_label' => 'WINTER',
            'gift.summer_label' => 'SOMMER',
            'gift.tiers' => [
                ['amount' => 'CHF 20', 'winter' => 'ein warmes Mittagessen für zwei Kinder', 'summer' => 'eine Gondelfahrt mit Zvieri für ein Kind'],
                ['amount' => 'CHF 50', 'winter' => 'ein Skitag mit Mittagessen für ein Kind', 'summer' => 'Bergfahrt für ein Kind und seine Begleitperson'],
                ['amount' => 'CHF 100', 'winter' => 'ein Skitag für ein Kind und seine Begleitperson', 'summer' => 'ein Tag für eine ganze Familie'],
                ['amount' => 'CHF 250', 'winter' => 'ein Tag im Dualski mit einer ausgebildeten Fachperson', 'summer' => 'ein Mittagessen für eine ganze Schulklasse'],
            ],

            // Donation form (RaiseNow embed)
            'donation.eyebrow' => 'JETZT SPENDEN',
            'donation.title' => 'Hier kannst du spenden!',
            'donation.unavailable_text' => 'Online-Spenden sind in Kürze hier möglich. Bis dahin kontaktiere uns gerne direkt.',
            'donation.preview_notice' => 'Vorschau – das Spendenformular ist noch nicht öffentlich sichtbar.',

            // Current activities
            'activities.eyebrow' => 'AKTUELL',
            'activities.title' => 'Was ist gerade aktuell?',
            'activities.text' => 'Bei steigenden Spenden können die Angebote auf Madrisa vergrössert werden.',
            'activities.items' => [
                ['image' => 'defaults/activity-restaurants.webp', 'icon' => 'restaurants', 'text' => 'Zur Zeit arbeiten 7 Personen mit Einschränkungen dank Spenden in den beiden Restaurants auf dem Berg.'],
                ['image' => 'defaults/activity-website.webp', 'icon' => 'website', 'text' => 'die neue Webseite soll auf die vielfältigen Möglichkeiten Aufmerksamkeit erwirken'],
                ['image' => 'defaults/activity-clubs.webp', 'icon' => 'clubs', 'text' => 'Die Visibilität von sämtlichen Clubs, die den Berg unterstützen soll erhöht werden'],
            ],

            // Impact in numbers
            'impact.eyebrow' => 'WIRKUNG IN ZAHLEN',
            'impact.title' => 'Impact in Zahlen',
            'impact.text' => 'Hilf uns, diese wunderschönen Momente zu erhöhen und vielen Familien glückliche Stunden zu ermöglichen.',
            'impact.stats' => [
                ['value' => '500+', 'label' => 'Unterstützte Personen'],
                ['value' => "1'200+", 'label' => 'Ermöglichte Tage am Berg'],
                ['value' => '350+', 'label' => 'Teilnehmende Kinder und Jugendliche'],
                ['value' => '5', 'label' => 'Partner'],
            ],

            // Stories
            'stories.eyebrow' => 'GESCHICHTEN',
            'stories.title' => 'Echte Geschichten, geteilte Momente',
            'stories.poster' => 'defaults/video-bg.webp',
            'stories.video_type' => 'none',
            'stories.testimonials' => [
                ['quote' => '«Unsere Familie hat den schönsten Tag erlebt.»', 'author' => 'Eine Familie'],
                ['quote' => '«Der Snowdream ist wirklich ein Traum, der wahr wird.»', 'author' => 'Ein Teilnehmer'],
            ],

            // Projects
            'projects.eyebrow' => 'BISHERIGE PROJEKTE',
            'projects.title' => 'Ein Berg für alle – in jeder Saison',
            'projects.cta_label' => 'Alle Projekte entdecken',
            'projects.cta_url' => '',
            'projects.items' => [
                ['title' => 'Summerdream', 'image' => 'defaults/project-summerdream.webp'],
                ['title' => 'Snowdream', 'image' => 'defaults/project-snowdream.webp'],
                ['title' => 'Skischule', 'image' => 'defaults/project-skischool.webp'],
                ['title' => 'Skilager', 'image' => 'defaults/project-skicamp.webp'],
            ],

            // About the foundation
            'about.eyebrow' => 'ÜBER DIE STIFTUNG',
            'about.title' => 'Stiftung mit Herz – Zweck und Ziel',
            'about.text' => 'Die Stiftung wurde am 17. Oktober 2018 gegründet und hat folgenden Zweck:',
            'about.nonprofit' => 'Die Stiftung ist gemeinnützig und damit sind Spenden auch steuerbefreit',
            'about.purposes' => [
                ['image' => 'defaults/purpose-infrastruktur.webp', 'icon' => 'infrastruktur', 'text' => 'Verbesserung der Infrastruktur - ausbauen für Barrierefreiheit'],
                ['image' => 'defaults/purpose-benefiz.webp', 'icon' => 'benefiz', 'text' => 'Organisation und Durchführung von Benefizanlässen'],
                ['image' => 'defaults/purpose-arbeit.webp', 'icon' => 'arbeit', 'text' => 'Integration von Menschen mit Beeinträchtigungen in den Arbeitsprozess von Klosters-Madrisa Bergbahnen AG'],
                ['image' => 'defaults/purpose-sport.webp', 'icon' => 'sport', 'text' => 'Sportanlässe und sonstige Veranstaltungen organisieren für Menschen mit Behinderungen'],
            ],

            // Board
            'board.title' => 'Welche Personen vertreten die Stiftung',
            'board.members' => [
                $member('Marie-Christine Jaeger', 'Präsidentin'),
                $member('Hans-Peter Domanig', 'Vizepräsident'),
                $member('Eugénie Bron', 'Mitglied'),
                $member('Georg Fromm', 'Mitglied'),
                $member('Esti Hannappel', 'Mitglied'),
                $member('Bruno Schefer', 'Mitglied', 'defaults/board-bruno-schefer.webp', 'Delegate Family Board Cosanum AG und Gründer von humancircularity.ch', '«Meine Vision: Inklusion möglich machen!»'),
                $member('Ruedi Weber', 'Mitglied'),
            ],

            // Network
            'network.eyebrow' => 'UNSER NETZWERK',
            'network.title' => 'Wir sind Madrisa',
            'network.image' => null,
            'network.image_title' => 'Wir sind Madrisa',
            'network.image_caption' => 'Madrisa Club · Madrisa Stern Club · Pro Madrisa · Stiftung Madrisa mit Herz',
            'network.partners' => [
                $partner('Cosanum'),
                $partner('Stiftung Cerebral'),
                $partner('Partner – placeholder'),
                $partner('Partner – placeholder'),
                $partner('Partner – placeholder'),
            ],

            // Stronger together
            'together.eyebrow' => 'GEMEINSAM',
            'together.title' => 'Stärker Zusammen und inklusiv',
            'together.text' => 'Mit deiner Unterstützung für die Stiftung Madrisa mit Herz trägst du dazu bei, Menschen mit Behinderungen sich in der schönen Natur von Madrisa frei zu bewegen und mit ihrer Familie und Freunden glücklich sein.',
            'together.cta_primary' => 'Jetzt spenden',
            'together.cta_secondary' => 'Kontakt',

            // FAQ
            'faq.title' => 'FAQ',
            'faq.items' => [
                ['question' => 'Was ist Madrisa mit Herz?', 'answer' => 'Madrisa mit Herz setzt sich dafür ein, dass Menschen mit Behinderungen und ihre Familien besondere Momente auf der Madrisa erleben können. Im Mittelpunkt stehen gemeinsame Erlebnisse, Begegnungen und die Freude, dabei zu sein.'],
                ['question' => 'Für wen engagiert sich die Stiftung?', 'answer' => 'Antwort folgt.'],
                ['question' => 'Wie kann Unterstützung angefragt werden?', 'answer' => 'Antwort folgt.'],
                ['question' => 'Wie kann Madrisa mit Herz unterstützt werden?', 'answer' => 'Antwort folgt.'],
                ['question' => 'Wie kann gespendet werden?', 'answer' => 'Antwort folgt.'],
                ['question' => 'Ist eine Zusammenarbeit als Unternehmen möglich?', 'answer' => 'Antwort folgt.'],
            ],

            // Contact form
            'contact.eyebrow' => 'KONTAKT',
            'contact.title' => 'Schick uns eine Nachricht',
            'contact.text' => 'Beschreibe kurz deine Situation oder dein Anliegen – die Stiftung meldet sich persönlich bei dir.',
            'contact.name_label' => 'Name',
            'contact.name_placeholder' => 'Vor- und Nachname',
            'contact.email_label' => 'E-Mail',
            'contact.email_placeholder' => 'name@beispiel.ch',
            'contact.phone_label' => 'Telefon (optional)',
            'contact.phone_placeholder' => '+41',
            'contact.subject_label' => 'Anliegen',
            'contact.subject_placeholder' => 'Bitte auswählen',
            'contact.subjects' => "Unterstützung anfragen\nSpenden\nZusammenarbeit als Unternehmen\nAllgemeine Anfrage",
            'contact.message_label' => 'Nachricht',
            'contact.message_placeholder' => 'Was können wir für dich tun?',
            'contact.consent' => 'Ich bin einverstanden, dass meine Angaben zur Bearbeitung der Anfrage verwendet werden.',
            'contact.submit' => 'Nachricht senden',
            'contact.success' => 'Danke für deine Nachricht! Die Stiftung meldet sich so bald wie möglich bei dir.',

            // Legal pages
            'legal.impressum' => '<h2>Impressum</h2><p>Stiftung Madrisa mit Herz<br>Adresse folgt</p><p>E-Mail: stiftung@madrisa.ch</p>',
            'legal.datenschutz' => '<h2>Datenschutzerklärung</h2><p>Text folgt.</p>',
        ];
    }

    /**
     * Settings shared by every language (no locale prefix).
     *
     * @return array<string, mixed>
     */
    public static function globals(): array
    {
        return [
            'site.locale_fr' => false,
            'site.locale_it' => false,
            'donation.live' => false,
            'donation.raisenow_url' => 'https://donate.raisenow.io/zptqy',
        ];
    }
}
