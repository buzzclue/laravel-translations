<?php

namespace Outhebox\TranslationsUI\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Outhebox\TranslationsUI\Http\Resources\TranslationFileResource;
use Outhebox\TranslationsUI\Models\TranslationFile;

/** @mixin TranslationFile */
class TranslationFileCollection extends ResourceCollection
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $this->collection->transform(function (TranslationFile $file) {
            return new TranslationFileResource($file);
        });

        return parent::toArray($request);
    }
}
