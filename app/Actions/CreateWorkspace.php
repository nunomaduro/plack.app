<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\WorkspaceType;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CreateWorkspace
{
    public function __construct(
        private CreateChannel $createChannel,
    ) {}

    public function handle(User $user, string $name, WorkspaceType $type = WorkspaceType::Private): Workspace
    {
        return DB::transaction(function () use ($user, $name, $type): Workspace {
            $workspace = $user->ownedWorkspaces()->create([
                'name' => $name,
                'type' => $type,
                'join_code' => $type === WorkspaceType::Public ? Str::random(64) : null,
            ]);

            $this->createChannel->handle($workspace, 'general');

            return $workspace;
        });
    }
}
