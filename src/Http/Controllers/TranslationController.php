<?php

namespace Outhebox\TranslationsUI\Http\Controllers;

use Exception;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;
use Outhebox\TranslationsUI\Modal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Outhebox\TranslationsUI\Models\Language;
use Outhebox\TranslationsUI\Models\Translation;
use Outhebox\TranslationsUI\TranslationsManager;
use Illuminate\Routing\Controller as BaseController;
use Outhebox\TranslationsUI\Events\TranslationChanged;
use Outhebox\TranslationsUI\Jobs\ImportTranslationsJob;
use Outhebox\TranslationsUI\Http\Resources\LanguageResource;
use Outhebox\TranslationsUI\Http\Resources\TranslationResource;
use Outhebox\TranslationsUI\Actions\CreateTranslationForLanguageAction;

class TranslationController extends BaseController
{
    public function publish(): Modal
    {
        return Inertia::modal('translations/modals/publish-translations', [
            'canPublish' => (bool) Translation::count() > 0,
            'isProductionEnv' => app()->isProduction(),
        ])->baseRoute('ltu.translation.index');
    }

    public function export(): RedirectResponse
    {
        try {
            app(TranslationsManager::class)->export();

            return back()->with('notification', [
                'type' => 'success',
                'body' => 'Translations have been exported successfully',
            ]);
        } catch (Exception $e) {
            return back()->with('notification', [
                'type' => 'error',
                'body' => $e->getMessage(),
            ]);
        }
    }

    public function import(): RedirectResponse
    {
        try {
            if (config('queue.default') === 'sync') {
                set_time_limit(0);
                // Run inline (blocking)
                Artisan::call('translations:import', [
                    '--no-overwrite' => true,
                ]);

                return back()->with('notification', [
                    'type' => 'success',
                    'body' => 'Translations have been imported successfully',
                ]);
            } else {
                // Run as queued job
                ImportTranslationsJob::dispatch();

                return back()->with('notification', [
                    'type' => 'success',
                    'body' => 'Import has started and is running in the background.',
                ]);
            }
        } catch (Exception $e) {
            return back()->with('notification', [
                'type' => 'error',
                'body' => $e->getMessage(),
            ]);
        }
    }

    public function download()
    {
        $downloadPath = app(TranslationsManager::class)->download();

        if (! $downloadPath) {
            return redirect()->route('ltu.translation.index')->with('notification', [
                'type' => 'error',
                'body' => 'Translations could not be downloaded',
            ]);
        }

        return response()->download($downloadPath, 'lang.zip');
    }

    public function index(): Response
    {
        $translations = Translation::with('language')
            ->withCount('phrases')
            ->withProgress()
            ->get();

        $allTranslations = $translations->where('source', false);
        $sourceTranslation = $translations->firstWhere('source', true);

        return Inertia::render('translations/index', [
            'translations' => TranslationResource::collection($allTranslations),
            'sourceTranslation' => $sourceTranslation ? TranslationResource::make($sourceTranslation) : null,
        ]);
    }

    public function create(): Modal
    {
        return Inertia::modal('translations/modals/add-translation', [
            'languages' => LanguageResource::collection(
                Language::whereNotIn('id', Translation::pluck('language_id')->toArray())->get()
            )->toArray(request()),
        ])->baseRoute('ltu.translation.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'languages' => 'required|array',
        ]);

        $languages = Language::whereIn('id', $request->input('languages'))->get();

        foreach ($languages as $language) {
            CreateTranslationForLanguageAction::execute($language);
        }

        return redirect()->route('ltu.translation.index')->with('notification', [
            'type' => 'success',
            'body' => 'Translations have been added successfully',
        ]);
    }

    public function toggle(Translation $translation): RedirectResponse
    {
        // Prevent disabling the default language
        if ($translation->is_default) {
            return back()->with('notification', [
                'type' => 'error',
                'body' => 'The default language cannot be disabled.',
            ]);
        }

        // If disabling and this is the last active translation → block
        if ($translation->status && Translation::where('status', true)->count() === 1) {
            return back()->with('notification', [
                'type' => 'error',
                'body' => 'At least one active language must remain enabled.',
            ]);
        }

        // Toggle status
        $translation->update([
            'status' => ! $translation->status,
        ]);

        TranslationChanged::dispatch($translation);

        return back()->with('notification', [
            'type' => 'success',
            'body' => 'Translation status updated successfully.',
        ]);
    }

    public function setDefault(Translation $translation): RedirectResponse
    {
        // Prevent setting inactive language as default
        if (! $translation->status) {
            return back()->with('notification', [
                'type' => 'error',
                'body' => 'You cannot set an inactive language as the default.',
            ]);
        }

        // If it's already default, just return
        if ($translation->is_default) {
            return back()->with('notification', [
                'type' => 'info',
                'body' => 'This language is already the default.',
            ]);
        }

        // Wrap in transaction for consistency
        \DB::transaction(function () use ($translation) {
            // Unset the current default
            Translation::where('is_default', true)->update(['is_default' => false]);

            // Set the new default
            $translation->update(['is_default' => true]);
        });

        // Optionally dispatch an event
        TranslationChanged::dispatch($translation);

        return back()->with('notification', [
            'type' => 'success',
            'body' => "{$translation->language->name} has been set as the default language.",
        ]);
    }

    public function destroy(Translation $translation): RedirectResponse
    {
        $translation->delete();

        return redirect()->route('ltu.translation.index')->with('notification', [
            'type' => 'success',
            'body' => 'Translation has been deleted successfully',
        ]);
    }

    public function destroy_multiple(Request $request): RedirectResponse
    {
        $request->validate([
            'selected_ids' => 'required|array',
        ]);

        $selectedTranslationIds = $request->input('selected_ids');
        $translations = Translation::whereIn('id', $selectedTranslationIds)->get();

        foreach ($translations as $translation) {
            $translation->delete();
        }

        return redirect()->route('ltu.translation.index')->with('notification', [
            'type' => 'success',
            'body' => 'Selected translations have been deleted successfully',
        ]);
    }
}
