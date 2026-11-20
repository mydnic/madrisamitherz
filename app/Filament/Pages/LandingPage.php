<?php

namespace App\Filament\Pages;

use App\Support\SiteContent;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Outerweb\FilamentSettings\Pages\Settings as BaseSettings;
use Outerweb\Settings\Models\Setting;

class LandingPage extends BaseSettings
{
    protected Width|string|null $maxContentWidth = Width::Full;

    public function defaultForm(Schema $schema): Schema
    {
        return parent::defaultForm($schema)->columns(1);
    }

    public function form(Schema $schema): Schema
    {
        $languages = site_locales();

        if (count($languages) > 1) {
            $tabs = array_map(
                fn (string $locale) => Tabs\Tab::make($locale)->schema([
                    Tabs::make('Content')->schema($this->getTabs($locale)),
                ]),
                $languages,
            );
        } else {
            $tabs = $this->getTabs($languages[0] ?? 'de');
        }

        $tabs[] = $this->globalTab();

        return $schema->components([Tabs::make('Settings')->schema($tabs)]);
    }

    /**
     * Not translated: site languages, one RaiseNow account and one go-live switch.
     */
    protected function globalTab(): Tabs\Tab
    {
        return Tabs\Tab::make('Einstellungen')
            ->schema([
                Toggle::make('site.locale_fr')
                    ->label('Französisch (FR) aktivieren')
                    ->helperText('Deutsch ist immer aktiv. Neue Sprachen starten mit dem deutschen Text: im neuen Sprach-Tab übersetzen. Die Seite lädt nach dem Speichern neu.'),
                Toggle::make('site.locale_it')
                    ->label('Italienisch (IT) aktivieren'),
                TextInput::make('donation.raisenow_url')
                    ->label('RaiseNow Formular-URL')
                    ->url()
                    ->helperText('RaiseNow Hub → Touchpoint-Lösung → «Auf Website einbinden»: die URL aus dem Code, z. B. https://donate.raisenow.io/zptqy (ohne ?lng=…, die Sprache wird automatisch gesetzt).'),
                Toggle::make('donation.live')
                    ->label('Spendenformular öffentlich')
                    ->helperText('Aus: nur eingeloggte Admins sehen das Formular (Vorschau). An: alle sehen es.'),
            ]);
    }

    /**
     * The settings package only upserts dotted rows, so removed repeater items would
     * survive and come back. Clear each list before it is written again.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        foreach ($this->form->getFlatComponents() as $component) {
            if ($component instanceof Repeater) {
                Setting::query()->where('key', 'like', $component->getName().'.%')->delete();
            }
        }

        return parent::mutateFormDataBeforeSave($data);
    }

    /**
     * Reload after saving so newly switched-on language tabs appear.
     */
    public function getRedirectUrl(): ?string
    {
        return static::getUrl();
    }

    public function getTabs(string $locale): array
    {
        $key = fn (string $name): string => $locale . '.' . $name;
        $image = fn (string $name, string $dir) => FileUpload::make($name)->image()->disk('public')->directory($dir);
        $icon = fn () => Select::make('icon')->options(SiteContent::ICONS)->required();
        $section = fn (string $prefix, bool $text = true) => array_filter([
            TextInput::make($key("{$prefix}.eyebrow")),
            TextInput::make($key("{$prefix}.title")),
            $text ? Textarea::make($key("{$prefix}.text"))->rows(3) : null,
        ]);

        return [
            Tabs\Tab::make('Meta, Header & Footer')
                ->schema([
                    TextInput::make($key('meta.title')),
                    Textarea::make($key('meta.description'))->rows(2),
                    $image($key('meta.favicon_image'), 'meta')->label('Favicon'),
                    TextInput::make($key('links.contact_email'))
                        ->label('Kontakt-E-Mail')
                        ->email()
                        ->helperText('Empfänger des Kontaktformulars.'),
                    TextInput::make($key('header.nav.home'))->label('Nav: Home'),
                    TextInput::make($key('header.nav.projects'))->label('Nav: Projekte'),
                    TextInput::make($key('header.nav.support'))->label('Nav: Unterstützung erhalten'),
                    TextInput::make($key('header.nav.about'))->label('Nav: Über uns'),
                    TextInput::make($key('header.nav.donate'))->label('Nav: Spenden'),
                    TextInput::make($key('header.nav.faq'))->label('Footer: FAQ'),
                    TextInput::make($key('header.nav.contact'))->label('Footer: Kontakt'),
                    TextInput::make($key('header.cta'))->label('Header-Button'),
                    TextInput::make($key('header.cta_short'))->label('Header-Button (Mobile)'),
                    TextInput::make($key('footer.copyright'))->helperText(':year wird durch das aktuelle Jahr ersetzt.'),
                    TextInput::make($key('footer.impressum'))->label('Link: Impressum'),
                    TextInput::make($key('footer.datenschutz'))->label('Link: Datenschutz'),
                ]),

            Tabs\Tab::make('Hero')
                ->schema([
                    Textarea::make($key('hero.title'))->rows(2),
                    Textarea::make($key('hero.text'))->rows(3),
                    TextInput::make($key('hero.cta_primary'))->label('Orange button'),
                    TextInput::make($key('hero.cta_secondary'))->label('White button'),
                    $image($key('hero.image'), 'hero')->helperText('Herz-Collage (transparentes PNG/WebP).'),
                ]),

            Tabs\Tab::make('Spenden')
                ->schema([
                    ...$section('gift', false),
                    $image($key('gift.image'), 'gift')->helperText('Polaroid (transparentes PNG/WebP).'),
                    TextInput::make($key('gift.winter_label')),
                    TextInput::make($key('gift.summer_label')),
                    Repeater::make($key('gift.tiers'))
                        ->label('Beträge')
                        ->schema([
                            TextInput::make('amount')->required(),
                            TextInput::make('winter')->required(),
                            TextInput::make('summer')->required(),
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['amount'] ?? null)
                        ->collapsible()
                        ->reorderable()
                        ->columns(3),
                    TextInput::make($key('donation.eyebrow')),
                    TextInput::make($key('donation.title')),
                    Textarea::make($key('donation.unavailable_text'))
                        ->rows(2)
                        ->helperText('Wird angezeigt, solange das Spendenformular nicht öffentlich ist.'),
                    TextInput::make($key('donation.preview_notice'))->helperText('Hinweis für Admins in der Vorschau.'),
                ]),

            Tabs\Tab::make('Aktuell')
                ->schema([
                    ...$section('activities'),
                    Repeater::make($key('activities.items'))
                        ->schema([$image('image', 'activities'), $icon(), Textarea::make('text')->rows(2)->required()])
                        ->reorderable()
                        ->columns(3),
                ]),

            Tabs\Tab::make('Wirkung')
                ->schema([
                    ...$section('impact'),
                    Repeater::make($key('impact.stats'))
                        ->schema([TextInput::make('value')->required(), TextInput::make('label')->required()])
                        ->reorderable()
                        ->columns(2),
                ]),

            Tabs\Tab::make('Geschichten')
                ->schema([
                    ...$section('stories', false),
                    $image($key('stories.poster'), 'stories')->label('Video-Titelbild'),
                    Radio::make($key('stories.video_type'))
                        ->label('Video')
                        ->options([
                            'none' => 'Kein Video (nur Bild)',
                            'youtube' => 'YouTube-Link',
                            'drive' => 'Google-Drive-Link',
                            'upload' => 'MP4 hochladen',
                        ])
                        ->default('none')
                        ->live(),
                    TextInput::make($key('stories.video_url'))
                        ->label('Video-Link')
                        ->url()
                        ->helperText(fn (Get $get) => $get($key('stories.video_type')) === 'drive'
                            ? 'Google Drive: Datei teilen mit "Jeder mit dem Link", dann den Link hier einfügen.'
                            : 'z. B. https://www.youtube.com/watch?v=…')
                        ->visible(fn (Get $get) => in_array($get($key('stories.video_type')), ['youtube', 'drive'], true)),
                    FileUpload::make($key('stories.video'))
                        ->label('Video (MP4)')
                        ->disk('public')
                        ->directory('stories')
                        ->acceptedFileTypes(['video/mp4'])
                        ->maxSize(102400)
                        ->visible(fn (Get $get) => $get($key('stories.video_type')) === 'upload'),
                    Repeater::make($key('stories.testimonials'))
                        ->schema([Textarea::make('quote')->rows(2)->required(), TextInput::make('author')->required()])
                        ->reorderable(),
                ]),

            Tabs\Tab::make('Projekte')
                ->schema([
                    ...$section('projects', false),
                    TextInput::make($key('projects.cta_label'))->label('Button'),
                    TextInput::make($key('projects.cta_url'))->label('Button-Link')->url()->helperText('Leer lassen, um den Button auszublenden.'),
                    Repeater::make($key('projects.items'))
                        ->schema([TextInput::make('title')->required(), $image('image', 'projects')])
                        ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                        ->collapsible()
                        ->reorderable()
                        ->columns(2),
                ]),

            Tabs\Tab::make('Über die Stiftung')
                ->schema([
                    ...$section('about'),
                    Repeater::make($key('about.purposes'))
                        ->label('Zweck')
                        ->schema([$image('image', 'about'), $icon(), Textarea::make('text')->rows(2)->required()])
                        ->reorderable()
                        ->columns(3),
                    TextInput::make($key('about.nonprofit'))->label('Hinweis gemeinnützig'),
                ]),

            Tabs\Tab::make('Stiftungsrat')
                ->schema([
                    TextInput::make($key('board.title')),
                    Repeater::make($key('board.members'))
                        ->schema([
                            TextInput::make('name')->required(),
                            TextInput::make('role')->required(),
                            Textarea::make('description')->rows(2),
                            TextInput::make('statement'),
                            $image('photo', 'board')->helperText('Ohne Foto wird ein Platzhalter gezeigt.'),
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                        ->collapsible()
                        ->reorderable()
                        ->columns(2),
                ]),

            Tabs\Tab::make('Netzwerk')
                ->schema([
                    ...$section('network', false),
                    $image($key('network.image'), 'network')->label('Bild «Wir sind Madrisa»'),
                    TextInput::make($key('network.image_title'))->label('Platzhalter-Titel')->helperText('Nur sichtbar, solange kein Bild hochgeladen ist.'),
                    TextInput::make($key('network.image_caption'))->label('Platzhalter-Text'),
                    Repeater::make($key('network.partners'))
                        ->schema([
                            TextInput::make('name')->required(),
                            TextInput::make('role')->label('Beschreibung'),
                            $image('logo', 'partners'),
                            TextInput::make('url')->label('Website')->url(),
                        ])
                        ->itemLabel(fn (array $state): ?string => $state['name'] ?? null)
                        ->collapsible()
                        ->reorderable()
                        ->columns(2),
                    ...$section('together'),
                    TextInput::make($key('together.cta_primary'))->label('Orange button'),
                    TextInput::make($key('together.cta_secondary'))->label('White button'),
                ]),

            Tabs\Tab::make('FAQ')
                ->schema([
                    TextInput::make($key('faq.title')),
                    Repeater::make($key('faq.items'))
                        ->schema([TextInput::make('question')->required(), Textarea::make('answer')->rows(3)->required()])
                        ->itemLabel(fn (array $state): ?string => $state['question'] ?? null)
                        ->collapsible()
                        ->reorderable(),
                ]),

            Tabs\Tab::make('Kontakt')
                ->schema([
                    ...$section('contact'),
                    TextInput::make($key('contact.name_label')),
                    TextInput::make($key('contact.name_placeholder')),
                    TextInput::make($key('contact.email_label')),
                    TextInput::make($key('contact.email_placeholder')),
                    TextInput::make($key('contact.phone_label')),
                    TextInput::make($key('contact.phone_placeholder')),
                    TextInput::make($key('contact.subject_label')),
                    TextInput::make($key('contact.subject_placeholder')),
                    Textarea::make($key('contact.subjects'))->label('Anliegen (eine Option pro Zeile)')->rows(4),
                    TextInput::make($key('contact.message_label')),
                    TextInput::make($key('contact.message_placeholder')),
                    Textarea::make($key('contact.consent'))->rows(2),
                    TextInput::make($key('contact.submit')),
                    Textarea::make($key('contact.success'))->label('Bestätigung nach dem Senden')->rows(2),
                ])
                ->columns(2),

            Tabs\Tab::make('Impressum & Datenschutz')
                ->schema([
                    RichEditor::make($key('legal.impressum'))->label('Impressum'),
                    RichEditor::make($key('legal.datenschutz'))->label('Datenschutz'),
                ]),
        ];
    }
}
