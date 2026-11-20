<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class LandingPageSettingsSeeder extends Seeder
{
    /**
     * Defaults live in App\Support\SiteContent and are written by `settings:sync`,
     * which only fills in keys that are missing — so re-seeding never overwrites
     * content the client has edited in the admin.
     */
    public function run(): void
    {
        Artisan::call('settings:sync', ['--prune-shadowed' => true]);
    }
}
