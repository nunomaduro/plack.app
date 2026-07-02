<?php

declare(strict_types=1);

use App\Actions\SendDirectMessage;
use App\Models\Conversation;
use App\Models\DirectMessage;
use App\Models\User;

it('may send a direct message', function (): void {
    $conversation = Conversation::factory()->create();
    $sender = User::factory()->create();

    $message = resolve(SendDirectMessage::class)->handle(
        $conversation,
        $sender,
        'Hey there!',
    );

    expect($message)
        ->toBeInstanceOf(DirectMessage::class)
        ->and($message->conversation->id)->toBe($conversation->id)
        ->and($message->sender->id)->toBe($sender->id)
        ->and($message->body)->toBe('Hey there!');
});
