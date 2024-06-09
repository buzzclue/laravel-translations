<?php

namespace Outhebox\TranslationsUI\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Outhebox\TranslationsUI\Http\Resources\LanguageResource;
use Outhebox\TranslationsUI\Models\Language;

/** @mixin Language */
class LanguageCollection extends ResourceCollection
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $this->collection->transform(function (Language $language) {
            return new LanguageResource($language);
        });

        return parent::toArray($request);
    }
}
