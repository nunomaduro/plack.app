<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\StatusPreset;

final readonly class DeleteStatusPreset
{
    public function handle(StatusPreset $statusPreset): void
    {
        $statusPreset->delete();
    }
}
