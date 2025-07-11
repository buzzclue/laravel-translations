<?php

namespace Outhebox\LaravelTranslations\Livewire;

use Livewire\Component;
use WireUi\Traits\WireUiActions;
use Illuminate\Contracts\View\View;
use Outhebox\LaravelTranslations\Models\Phrase;
use Outhebox\LaravelTranslations\Models\Translation;
use Outhebox\LaravelTranslations\Http\Traits\NotifiesWithWireUi;

class PhraseForm extends Component
{
    use WireUiActions, NotifiesWithWireUi;

    public $content = '';

    public Phrase $phrase;

    public Translation $translation;

    public function mount(Translation $translation, Phrase $phrase): void
    {
        $this->phrase = $phrase;
        $this->translation = $translation;

        $this->content = $phrase?->value;
    }

    public function save(): void
    {
        if (blank($this->content)) {
            $this->notifyError('Please enter a translation.');

            return;
        }

        if (!blank($this->phrase->source) && $this->missingParameters()) {
            $this->notifyError('Required parameters are missing.');

            return;
        }

        $this->phrase->value = trim($this->content);

        $this->phrase->save();

        $this->notifySuccess('Phrase updated successfully!');

        $nextPhrase = $this->translation->phrases()
            ->where('id', '>', $this->phrase->id)
            ->whereNull('value')
            ->first();

        if ($nextPhrase) {
            $this->redirect(route('translations_ui.phrases.show', [
                'phrase' => $nextPhrase,
                'translation' => $this->translation,
            ]));

            return;
        }

        $this->redirect(route('translations_ui.phrases.index', $this->translation));
    }

    public function missingParameters(): bool
    {
        if (is_array($this->phrase->source->parameters)) {
            foreach ($this->phrase->source->parameters as $parameter) {
                if (!str_contains($this->content, ":$parameter")) {
                    return true;
                }
            }
        }

        return false;
    }

    public function render(): View
    {
        return view('translations::livewire.phrase-form');
    }
}
