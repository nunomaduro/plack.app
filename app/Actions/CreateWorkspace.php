<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

final readonly class CreateWorkspace
{
    public function __construct(
        private CreateChannel $createChannel,
    ) {}

    public function handle(User $user, string $name): Workspace
    {
        return DB::transaction(function () use ($user, $name): Workspace {
            $workspace = $user->workspaces()->create([
                'name' => $name,
            ]);

            $this->createChannel->handle($workspace, 'general');

            return $workspace;
        });
    }
}
