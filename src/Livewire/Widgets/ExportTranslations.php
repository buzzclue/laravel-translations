<?php

namespace Outhebox\LaravelTranslations\Livewire\Widgets;

use Livewire\Component;
use Illuminate\Contracts\View\View;
use Outhebox\LaravelTranslations\TranslationsManager;
use Outhebox\LaravelTranslations\Http\Traits\NotifiesWithWireUi;

class ExportTranslations extends Component
{
    use NotifiesWithWireUi;

    public function export(): void
    {
        app(TranslationsManager::class)->export();

        $this->notifySuccess('Translations exported successfully!');
    }

    public function render(): View
    {
        return view('translations::livewire.widgets.export-translations');
    }
}
