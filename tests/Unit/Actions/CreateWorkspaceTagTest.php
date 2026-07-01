<?php

declare(strict_types=1);

use App\Actions\CreateWorkspaceTag;
use App\Models\User;
use App\Models\WorkspaceTag;

it('may create a workspace tag', function (): void {
    $user = User::factory()->create();

    $tag = resolve(CreateWorkspaceTag::class)->handle(
        $user,
        'engineering',
    );

    expect($tag)
        ->toBeInstanceOf(WorkspaceTag::class)
        ->and($tag->owner->id)->toBe($user->id)
        ->and($tag->name)->toBe('engineering');
});
