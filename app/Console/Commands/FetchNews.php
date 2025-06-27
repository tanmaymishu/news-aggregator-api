<?php

namespace App\Console\Commands;

use App\Services\News\Sourcable;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class FetchNews extends Command
{
    const sources = ['newsapi', 'theguardian', 'nytimes'];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:fetch {source?*} {--S|search=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch news from: NewsApi, TheGuardian, NYTimes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $filteredSources = $this->validateSources();

        // If even after validation, all the user-provided
        // sources are invalid, fallback to the defaults as
        // defined in the sources constant.
        if ($filteredSources->count() === 0) {
            $filteredSources = collect(self::sources);
        }

        $this->info('Fetching new articles. This may take a while, please be patient.');
        $filteredSources->each(function ($source) {
            $this->info("Fetching for source {$source}");
            $this->collectFromDataSource(app()->make($source));
        });
    }

    private function collectFromDataSource(Sourcable $source): void
    {
        $source->search($this->option('search'))
            ->normalize()
            ->save();
    }

    private function validateSources(): Collection
    {
        $args = $this->argument('source');

        // If no arg is provided, all default sources are valid.
        if (count($args) === 0) {
            return collect(self::sources);
        }

        // Normalize the sources to lowercase string
        $sources = collect($args)->map(fn ($arg) => strtolower($arg));

        // If args are provided, take the intersected ones and discard
        // any missing source/misspelled source.
        return $sources->intersect(self::sources);
    }
}
