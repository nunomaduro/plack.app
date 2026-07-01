<?php

declare(strict_types=1);

use App\Actions\CreateUserTag;
use App\Models\UserTag;
use App\Models\Workspace;

it('may create a user tag', function (): void {
    $workspace = Workspace::factory()->create();

    $tag = resolve(CreateUserTag::class)->handle(
        $workspace,
        'frontend',
    );

    expect($tag)
        ->toBeInstanceOf(UserTag::class)
        ->and($tag->workspace->id)->toBe($workspace->id)
        ->and($tag->name)->toBe('frontend');
});
