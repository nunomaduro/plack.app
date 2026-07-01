<?php

declare(strict_types=1);

use App\Actions\DeleteWorkspace;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Illuminate\Support\Facades\DB;

it('deletes a workspace with its members and invitations', function (): void {
    $workspace = Workspace::factory()->create();
    $workspace->members()->attach(User::factory()->create());
    WorkspaceInvitation::factory()->for($workspace)->create();

    resolve(DeleteWorkspace::class)->handle($workspace);

    expect(Workspace::query()->whereKey($workspace->id)->exists())->toBeFalse()
        ->and(DB::table('workspace_user')->count())->toBe(0)
        ->and(WorkspaceInvitation::query()->count())->toBe(0);
});
