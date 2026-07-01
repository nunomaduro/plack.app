<?php

declare(strict_types=1);

use App\Actions\DetachWorkspaceTag;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceTag;

it('may detach a workspace tag from a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = WorkspaceTag::factory()->for($user, 'owner')->create();

    $workspace->tags()->attach($tag->id);

    resolve(DetachWorkspaceTag::class)->handle($workspace, $tag);

    expect($workspace->tags()->count())->toBe(0);
});
