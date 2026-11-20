<?php

namespace Tests\Feature;

use App\Filament\Pages\LandingPage;
use App\Models\ContactMessage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Artisan::call('settings:sync');
    }

    public function test_landing_page_renders_seeded_content(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('Spende einen glücklichen Moment')
            ->assertSee('ein Tag im Dualski mit einer ausgebildeten Fachperson')
            ->assertSee('Marie-Christine Jaeger')
            ->assertSee('Ist eine Zusammenarbeit als Unternehmen möglich?')
            ->assertSee('/storage/defaults/board-bruno-schefer.webp', false)
            ->assertSee('/storage/defaults/hero-heart.webp', false);
    }

    public function test_sync_seeds_every_language_and_default_images(): void
    {
        $this->assertSame('Spende einen glücklichen Moment', setting('it.gift.title'));
        $this->assertSame('defaults/activity-website.webp', setting('fr.activities.items.1.image'));
        Storage::disk('public')->assertExists('defaults/purpose-sport.webp');
    }

    public function test_languages_are_switched_on_in_the_admin(): void
    {
        $this->get('/')->assertDontSee('/language/fr');

        setting(['site.locale_fr' => true, 'fr.gift.title' => 'Offre un moment de bonheur']);

        $this->get('/')->assertSee('/language/fr');
        $this->get('/language/fr');
        $this->get('/')->assertSee('Offre un moment de bonheur');

        $this->get('/language/it');
        $this->get('/')->assertSee('Offre un moment'); // IT is off: session keeps FR
    }

    public function test_video_links_become_embeds(): void
    {
        setting(['de.stories.video_type' => 'youtube', 'de.stories.video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ&t=10']);
        $this->assertSame(['iframe', 'https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0'], video_embed());

        setting(['de.stories.video_url' => 'https://youtu.be/dQw4w9WgXcQ']);
        $this->assertSame('https://www.youtube-nocookie.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0', video_embed()[1]);

        setting(['de.stories.video_type' => 'drive', 'de.stories.video_url' => 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz012345/view?usp=sharing']);
        $this->assertSame(['iframe', 'https://drive.google.com/file/d/1AbCdEfGhIjKlMnOpQrStUvWxYz012345/preview'], video_embed());

        setting(['de.stories.video_url' => 'not a link']);
        $this->assertNull(video_embed());

        setting(['de.stories.video_type' => 'none']);
        $this->assertNull(video_embed());
    }

    public function test_removed_faq_items_stay_removed_after_saving(): void
    {
        $this->actingAs(User::factory()->create());

        $component = Livewire::test(LandingPage::class);
        $items = $component->get('data.de.faq.items');
        $component->set('data.de.faq.items', [array_key_first($items) => reset($items)])
            ->call('save')
            ->assertHasNoErrors();

        $this->assertCount(1, setting('de.faq.items'));
    }

    public function test_admin_pages_render(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get('/admin/landing-page')->assertOk()->assertSee('RaiseNow')->assertSee('Stiftungsrat');
        $this->get('/admin/contact-messages')->assertOk();
    }

    public function test_raisenow_form_is_preview_only_until_live(): void
    {
        $this->get('/')
            ->assertDontSee('donate.raisenow.io')
            ->assertSee('Online-Spenden sind in Kürze hier möglich.');

        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertSee('https:\/\/donate.raisenow.io\/zptqy?lng=de', false)
            ->assertSee('Vorschau');
    }

    public function test_raisenow_form_is_public_once_live_in_the_visitor_language(): void
    {
        setting(['donation.live' => true, 'donation.raisenow_url' => 'https://donate.raisenow.io/abcd?lng=en', 'site.locale_fr' => true]);

        $this->get('/')->assertSee('https:\/\/donate.raisenow.io\/abcd?lng=de', false)->assertDontSee('Vorschau');

        $this->get('/language/fr');
        $this->get('/')->assertSee('https:\/\/donate.raisenow.io\/abcd?lng=fr', false);
    }

    public function test_contact_form_stores_and_mails_the_message(): void
    {
        $this->post('/kontakt', [
            'name' => 'Anna Muster',
            'email' => 'anna@example.ch',
            'subject' => 'Spenden',
            'message' => 'Hallo Stiftung',
            'consent' => '1',
        ])->assertRedirect(url('/').'#kontakt');

        $this->assertDatabaseHas('contact_messages', ['email' => 'anna@example.ch', 'subject' => 'Spenden', 'locale' => 'de']);

        $sent = app('mailer')->getSymfonyTransport()->messages();
        $this->assertCount(1, $sent);
        $this->assertSame('stiftung@madrisa.ch', $sent[0]->getOriginalMessage()->getTo()[0]->getAddress());
        $this->assertSame('anna@example.ch', $sent[0]->getOriginalMessage()->getReplyTo()[0]->getAddress());

        $this->get('/')->assertSee('Danke für deine Nachricht!');
    }

    public function test_contact_form_rejects_missing_consent_and_bots(): void
    {
        $this->post('/kontakt', ['name' => 'A', 'email' => 'a@example.ch', 'message' => 'x'])
            ->assertSessionHasErrorsIn('contact', ['consent']);

        $this->post('/kontakt', ['name' => 'Bot', 'email' => 'b@example.ch', 'message' => 'spam', 'consent' => '1', 'website' => 'http://spam']);

        $this->assertSame(0, ContactMessage::count());
    }

    public function test_legal_pages_render_admin_content(): void
    {
        setting(['de.legal.impressum' => '<h2>Impressum</h2><p>Musterstrasse 1</p><script>alert(1)</script>']);

        $this->get('/impressum')->assertOk()->assertSee('Musterstrasse 1')->assertDontSee('<script>', false);
        $this->get('/datenschutz')->assertOk()->assertSee('Datenschutzerklärung');
    }
}
