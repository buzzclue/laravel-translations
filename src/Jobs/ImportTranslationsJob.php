<?php

namespace Outhebox\TranslationsUI\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Artisan;


class ImportTranslationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Artisan::call('translations:import', [
            '--no-overwrite' => true,
        ]);
    }
}
