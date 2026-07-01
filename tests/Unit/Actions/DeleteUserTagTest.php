<?php

declare(strict_types=1);

use App\Actions\DeleteUserTag;
use App\Models\UserTag;

it('may delete a user tag', function (): void {
    $tag = UserTag::factory()->create();

    resolve(DeleteUserTag::class)->handle($tag);

    expect(UserTag::query()->whereKey($tag->id)->exists())->toBeFalse();
});
