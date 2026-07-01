<?php

declare(strict_types=1);

use App\Actions\SendMessage;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('may send messages', function (): void {
    $channel = Channel::factory()->create();
    $sender = User::factory()->create();

    $message = resolve(SendMessage::class)->handle(
        $channel,
        $sender,
        'Hello, world!',
    );

    expect($message)
        ->toBeInstanceOf(Message::class)
        ->and($message->channel->id)->toBe($channel->id)
        ->and($message->sender->id)->toBe($sender->id)
        ->and($message->body)->toBe('Hello, world!');
});

it('may send messages with attachments', function (): void {
    Storage::fake('local');

    $channel = Channel::factory()->create();
    $sender = User::factory()->create();

    $message = resolve(SendMessage::class)->handle(
        $channel,
        $sender,
        null,
        [UploadedFile::fake()->image('screenshot.png')],
    );

    $attachment = $message->attachments->sole();

    expect($message->body)->toBeNull()
        ->and($attachment->user_id)->toBe($sender->id)
        ->and($attachment->workspace_id)->toBe($channel->workspace_id)
        ->and($attachment->original_filename)->toBe('screenshot.png')
        ->and($attachment->mime_type)->toBe('image/png')
        ->and($attachment->storage_key)->toStartWith("workspaces/{$channel->workspace_id}/attachments/")
        ->and($attachment->storage_key)->not->toContain('screenshot');

    Storage::disk('local')->assertExists($attachment->storage_key);
});
