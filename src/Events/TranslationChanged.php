<?php

namespace Outhebox\TranslationsUI\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Outhebox\TranslationsUI\Models\Translation;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TranslationChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Translation $translation;

    public function __construct(Translation $translation)
    {
        $this->translation = $translation;
    }

    public function broadcastOn(): array
    {
        return [new Channel('translations')];
    }

    public function broadcastAs(): string
    {
        return 'translations.status.changed';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->translation->id,
            'status' => $this->translation->status,
            'language' => $this->translation->language,
        ];
    }
}
