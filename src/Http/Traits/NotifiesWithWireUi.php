<?php

namespace Outhebox\LaravelTranslations\Http\Traits;

use WireUi\Traits\WireUiActions;

trait NotifiesWithWireUi
{
    use WireUiActions;

    public function notifySuccess(string $description = '', string $title = 'Success!'): void
    {
        $this->notification()->send([
            'title' => $title,
            'description' => $description,
            'icon' => 'success',
        ]);
    }

    public function notifyError(string $description = '', string $title = 'Error!'): void
    {
        $this->notification()->send([
            'title' => $title,
            'description' => $description,
            'icon' => 'error',
        ]);
    }

    public function notifyInfo(string $description = '', string $title = 'Warning!'): void
    {
        $this->notification()->send([
            'title' => $title,
            'description' => $description,
            'icon' => 'info',
        ]);
    }
}
