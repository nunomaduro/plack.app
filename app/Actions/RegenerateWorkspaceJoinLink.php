<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workspace;
use Illuminate\Support\Str;

final readonly class RegenerateWorkspaceJoinLink
{
    public function handle(Workspace $workspace): void
    {
        $workspace->update([
            'join_code' => Str::random(64),
        ]);
    }
}
