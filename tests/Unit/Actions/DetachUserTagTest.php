<?php

declare(strict_types=1);

use App\Actions\DetachUserTag;
use App\Models\User;
use App\Models\UserTag;
use App\Models\Workspace;

it('may detach a user tag from a user', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create();

    $tag->users()->attach($user->id);

    resolve(DetachUserTag::class)->handle($user, $tag);

    expect($tag->users()->count())->toBe(0);
});
