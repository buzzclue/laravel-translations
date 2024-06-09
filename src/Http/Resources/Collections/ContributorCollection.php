<?php

namespace Outhebox\TranslationsUI\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Outhebox\TranslationsUI\Http\Resources\ContributorResource;
use Outhebox\TranslationsUI\Models\Contributor;

/** @mixin Contributor */
class ContributorCollection extends ResourceCollection
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $this->collection->transform(function (Contributor $contributor) {
            return new ContributorResource($contributor);
        });

        return parent::toArray($request);
    }
}
