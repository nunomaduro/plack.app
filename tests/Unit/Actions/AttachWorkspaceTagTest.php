<?php

declare(strict_types=1);

use App\Actions\AttachWorkspaceTag;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceTag;

it('may attach a workspace tag to a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = WorkspaceTag::factory()->for($user, 'owner')->create();

    resolve(AttachWorkspaceTag::class)->handle($workspace, $tag);

    expect($workspace->tags()->count())->toBe(1)
        ->and($workspace->tags->first()->id)->toBe($tag->id);
});

it('does not duplicate when attaching the same tag twice', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = WorkspaceTag::factory()->for($user, 'owner')->create();

    $action = resolve(AttachWorkspaceTag::class);
    $action->handle($workspace, $tag);
    $action->handle($workspace, $tag);

    expect($workspace->tags()->count())->toBe(1);
});
