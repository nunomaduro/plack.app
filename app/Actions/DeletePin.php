<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Pin;

final readonly class DeletePin
{
    public function handle(Pin $pin): void
    {
        $pin->delete();
    }
}
