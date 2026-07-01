<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

final readonly class DeleteWorkspace
{
    public function handle(Workspace $workspace): void
    {
        DB::transaction(function () use ($workspace): void {
            $workspace->members()->detach();
            $workspace->invitations()->delete();
            $workspace->delete();
        });
    }
}
