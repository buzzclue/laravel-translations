<?php

namespace Outhebox\TranslationsUI\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Outhebox\TranslationsUI\Http\Resources\TranslationResource;
use Outhebox\TranslationsUI\Models\Translation;

/**
 * @mixin Translation
 *
 * @property mixed $progress
 */
class TranslationCollection extends ResourceCollection
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $this->collection->transform(function (Translation $translation) {
            return new TranslationResource($translation);
        });

        return parent::toArray($request);
    }
}
