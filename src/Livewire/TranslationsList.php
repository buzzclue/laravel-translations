<?php

namespace Outhebox\LaravelTranslations\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use WireUi\Traits\WireUiActions;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Outhebox\LaravelTranslations\Models\Translation;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Outhebox\LaravelTranslations\Http\Traits\NotifiesWithWireUi;

class TranslationsList extends Component
{
    use WithPagination, NotifiesWithWireUi, WireUiActions;

    public $search;

    protected $listeners = [
        'translationCreated' => '$refresh',
    ];

    public function getTranslations(): LengthAwarePaginator
    {
        return Translation::orderByDesc('source')
            ->when($this->search, function ($query) {
                $query->whereHas('language', function ($query) {
                    $query->where(function ($query) {
                        $query->where('name', 'like', "%{$this->search}%")
                            ->orWhere('code', 'like', "%{$this->search}%");
                    });
                });
            })
            ->paginate(12)->onEachSide(0);
    }

    public function getTranslationProgressPercentage(Translation $translation): float
    {
        $phrases = $translation->phrases()->toBase()
            ->selectRaw('COUNT(CASE WHEN value IS NOT NULL THEN 1 END) AS translated')
            ->selectRaw('COUNT(CASE WHEN value IS NULL THEN 1 END) AS untranslated')
            ->selectRaw('COUNT(*) AS total')
            ->first();

        return round(($phrases->translated / $phrases->total) * 100, 2);
    }

    public function confirmDelete(Translation $translation): void
    {
        $this->dialog()->confirm([
            'title' => 'Are you Sure?',
            'description' => 'This action will delete the translation and all phrases, are you sure you want to continue?',
            'method' => 'delete',
            'style' => 'inline',
            'icon' => 'error',
            'params' => $translation,
            'acceptLabel' => 'Yes, delete it',
        ]);
    }

    public function delete(Translation $translation): void
    {
        DB::transaction(function () use ($translation) {
            $translation->phrases()->delete();
            $translation->delete();

            $this->notifySuccess('Translation deleted successfully!');
        });
    }

    public function render(): View
    {
        return view('translations::livewire.translations-list', [
            'translations' => $this->getTranslations(),
        ]);
    }
}
