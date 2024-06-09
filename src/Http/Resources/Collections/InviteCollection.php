<?php

namespace Outhebox\TranslationsUI\Http\Resources\Collections;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Outhebox\TranslationsUI\Http\Resources\InviteResource;
use Outhebox\TranslationsUI\Models\Invite;

/** @mixin Invite */
class InviteCollection extends ResourceCollection
{
    public static $wrap = null;

    public function toArray($request): array
    {
        $this->collection->transform(function (Invite $invite) {
            return new InviteResource($invite);
        });

        return parent::toArray($request);
    }
}
