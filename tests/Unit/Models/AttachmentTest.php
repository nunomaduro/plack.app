<?php

declare(strict_types=1);

use App\Models\Attachment;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;

test('to array', function (): void {
    $attachment = Attachment::factory()->create()->fresh();

    expect(array_keys($attachment->toArray()))
        ->toBe([
            'id',
            'workspace_id',
            'message_id',
            'user_id',
            'original_filename',
            'mime_type',
            'size_bytes',
            'storage_key',
            'created_at',
            'updated_at',
        ]);
});

test('it belongs to a workspace, message and user', function (): void {
    $attachment = Attachment::factory()->create();

    expect($attachment->workspace)->toBeInstanceOf(Workspace::class)
        ->and($attachment->message)->toBeInstanceOf(Message::class)
        ->and($attachment->user)->toBeInstanceOf(User::class);
});
