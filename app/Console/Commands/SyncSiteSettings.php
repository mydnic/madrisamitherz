<?php

namespace App\Console\Commands;

use App\Support\SiteContent;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SyncSiteSettings extends Command
{
    use ConfirmableTrait;

    protected $signature = 'settings:sync
        {--overwrite : Also replace keys that already have a value (discards client edits)}
        {--prune-shadowed : Delete legacy rows that shadow flattened repeater keys}
        {--dry-run : Show what would change without writing anything}
        {--force : Skip the confirmation prompt when running in production}';

    protected $description = 'Fill in any missing site content settings so the admin panel is ready to edit. Safe to run on every deploy.';

    public function handle(): int
    {
        $overwrite = (bool) $this->option('overwrite');
        $dryRun = (bool) $this->option('dry-run');

        // Only overwriting is destructive, so that is the only path that needs confirming.
        if ($overwrite && ! $dryRun && ! $this->confirmToProceed('Overwriting discards content edited in the admin.')) {
            return self::FAILURE;
        }

        $settings = app('setting');
        $toWrite = [];
        $kept = 0;

        // Every supported language is pre-filled (German copy until translated), so a
        // language switched on in the admin opens with a complete form.
        $wanted = SiteContent::globals();
        foreach (config('app.supported_locales') as $locale) {
            foreach (SiteContent::defaults() as $key => $value) {
                $wanted["{$locale}.{$key}"] = $value;
            }
        }

        foreach ($wanted as $key => $value) {
            // has() matches the key itself *and* its dotted children, so repeater
            // values stored as leaf rows are correctly seen as already present.
            if (! $overwrite && $settings->has($key)) {
                $kept++;

                continue;
            }

            $toWrite[$key] = $value;
        }

        $shadowed = $this->option('prune-shadowed') ? $this->shadowedKeys() : collect();

        if ($dryRun) {
            $this->line('<comment>Dry run — nothing written.</comment>');
            $this->line(sprintf('Would write %d key(s), keep %d existing.', count($toWrite), $kept));
            foreach (array_keys($toWrite) as $key) {
                $this->line("  + {$key}");
            }
            foreach ($shadowed as $key) {
                $this->line("  - {$key} (shadowed)");
            }

            return self::SUCCESS;
        }

        $this->copyDefaultImages($toWrite);

        if ($toWrite !== []) {
            // Write through the package setter so arrays are flattened exactly like the
            // admin form does. A raw DB insert would shadow the form's leaf rows.
            $settings->set($toWrite);
        }

        if ($shadowed->isNotEmpty()) {
            DB::table('settings')->whereIn('key', $shadowed)->delete();
        }

        $this->info(sprintf(
            'Settings synced: %d written, %d left untouched%s.',
            count($toWrite),
            $kept,
            $shadowed->isNotEmpty() ? ", {$shadowed->count()} shadowed row(s) pruned" : ''
        ));

        return self::SUCCESS;
    }

    /**
     * Seeded image settings point to defaults/<file> on the public disk: put the
     * bundled design image there so the admin upload fields show it.
     */
    protected function copyDefaultImages(array $values): void
    {
        $disk = Storage::disk('public');

        collect(Arr::dot($values))
            ->filter(fn ($value) => is_string($value) && str_starts_with($value, 'defaults/'))
            ->unique()
            ->reject(fn (string $path) => $disk->exists($path))
            ->each(fn (string $path) => $disk->put($path, file_get_contents(public_path('images/'.basename($path)))));
    }

    /**
     * Rows whose key is a parent of other rows (e.g. "de.x.cards" alongside
     * "de.x.cards.0.title"). These make Setting::get() return a malformed array.
     */
    protected function shadowedKeys(): Collection
    {
        $keys = DB::table('settings')->pluck('key');

        return $keys
            ->filter(fn (string $key) => $keys->contains(fn (string $other) => str_starts_with($other, $key . '.')))
            ->values();
    }
}
