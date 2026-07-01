<?php

declare(strict_types=1);

use App\Actions\AttachUserTag;
use App\Models\User;
use App\Models\UserTag;
use App\Models\Workspace;

it('may attach a user tag to a user', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create();

    resolve(AttachUserTag::class)->handle($user, $tag);

    expect($tag->users()->count())->toBe(1)
        ->and($tag->users->first()->id)->toBe($user->id);
});

it('does not duplicate when attaching the same tag twice', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create();

    $action = resolve(AttachUserTag::class);
    $action->handle($user, $tag);
    $action->handle($user, $tag);

    expect($tag->users()->count())->toBe(1);
});
