<?php

namespace Outhebox\TranslationsUI\Actions;

use Outhebox\TranslationsUI\Models\Language;
use Outhebox\TranslationsUI\Models\Translation;

class CreateTranslationForLanguageAction
{
    public static function execute(Language $language): void
    {
        $translation = Translation::create([
            'source' => false,
            'language_id' => $language->id,
            'is_default' => false,
            'status' => false,
        ]);

        CopyPhrasesFromSourceAction::execute($translation);
    }
}
