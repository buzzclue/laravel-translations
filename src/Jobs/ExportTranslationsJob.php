<?php

namespace Outhebox\TranslationsUI\Jobs;

use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Outhebox\TranslationsUI\TranslationsManager;

class ExportTranslationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        app(TranslationsManager::class)->export();
    }
}
