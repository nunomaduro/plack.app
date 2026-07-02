<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\WorkspaceType;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Queries\ListWorkspace;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Inertia\Inertia;
use Inertia\Response;

final readonly class WorkspaceSettingsController
{
    public function __invoke(#[CurrentUser] User $user, Workspace $workspace, ListWorkspace $listWorkspace): Response
    {
        $workspace->load(['channels' => fn (HasMany $channels) => $channels->latest(), 'owner', 'members', 'invitations']);

        return Inertia::render('workspace/settings', [
            'workspace' => [
                'id' => $workspace->id,
                'name' => $workspace->name,
                'slug' => $workspace->slug,
                'type' => $workspace->type->value,
                'channels' => $workspace->channels->map->only('id')->values(),
            ],
            'owner' => $workspace->owner->only('id', 'name', 'email'),
            'members' => $workspace->members->map->only('id', 'name', 'email')->values(),
            'invitations' => $workspace->type === WorkspaceType::Private ? $workspace->invitations->map(fn (WorkspaceInvitation $invitation): array => [
                'code' => $invitation->code,
                'email' => $invitation->email,
            ])->values() : [],
            'publicJoinUrl' => $workspace->type === WorkspaceType::Public
                ? route('workspace.join', (string) $workspace->join_code)
                : null, // @codeCoverageIgnore
            'workspaces' => $listWorkspace->get($user),
        ]);
    }
}
