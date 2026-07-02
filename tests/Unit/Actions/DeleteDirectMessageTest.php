<?php

declare(strict_types=1);

use App\Actions\DeleteDirectMessage;
use App\Models\DirectMessage;

it('may delete a direct message', function (): void {
    $message = DirectMessage::factory()->create();

    $action = resolve(DeleteDirectMessage::class);
    $action->handle($message);

    expect(DirectMessage::query()->whereKey($message->id)->exists())->toBeFalse();
});
