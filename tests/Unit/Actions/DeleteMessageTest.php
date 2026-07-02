<?php

declare(strict_types=1);

use App\Actions\DeleteMessage;
use App\Models\Message;

it('may delete a message', function (): void {
    $message = Message::factory()->create();

    $action = resolve(DeleteMessage::class);

    $action->handle($message);

    expect(Message::query()->whereKey($message->id)->exists())->toBeFalse();
});
