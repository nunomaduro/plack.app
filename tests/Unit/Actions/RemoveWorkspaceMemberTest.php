<?php

declare(strict_types=1);

use App\Actions\RemoveWorkspaceMember;
use App\Models\User;
use App\Models\Workspace;

it('detaches the member from the workspace', function (): void {
    $workspace = Workspace::factory()->create();
    $user = User::factory()->create();
    $workspace->members()->attach($user);

    resolve(RemoveWorkspaceMember::class)->handle($workspace, $user);

    expect($workspace->members()->whereKey($user->id)->exists())->toBeFalse();
});
