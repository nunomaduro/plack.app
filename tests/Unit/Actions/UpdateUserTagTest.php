<?php

declare(strict_types=1);

use App\Actions\UpdateUserTag;
use App\Models\UserTag;

it('may update a user tag name', function (): void {
    $tag = UserTag::factory()->create([
        'name' => 'frontend',
    ]);

    resolve(UpdateUserTag::class)->handle($tag, 'backend');

    expect($tag->refresh()->name)->toBe('backend');
});
