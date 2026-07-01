<?php

declare(strict_types=1);

use App\Actions\DeleteReaction;
use App\Models\Reaction;

it('may delete a reaction', function (): void {
    $reaction = Reaction::factory()->create();

    resolve(DeleteReaction::class)->handle($reaction);

    expect(Reaction::query()->whereKey($reaction->id)->exists())->toBeFalse()
        ->and(Reaction::withTrashed()->whereKey($reaction->id)->exists())->toBeTrue();
});
