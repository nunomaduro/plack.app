<?php

declare(strict_types=1);

use App\Models\Attachment;

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
