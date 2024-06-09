<?php

namespace Outhebox\TranslationsUI\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Outhebox\TranslationsUI\Http\Resources\PhraseResource;
use Outhebox\TranslationsUI\Models\Phrase;

/** @mixin Phrase */
class PhraseCollection extends ResourceCollection
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $this->collection->transform(function (Phrase $phrase) {
            return new PhraseResource($phrase);
        });

        return parent::toArray($request);
    }
}
