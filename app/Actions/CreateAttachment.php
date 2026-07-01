<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Attachment;
use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

final readonly class CreateAttachment
{
    public function handle(Channel $channel, Message $message, User $user, UploadedFile $file): Attachment
    {
        $name = (string) Str::ulid();

        $extension = $file->extension();
        $filename = $extension === '' ? $name : sprintf('%s.%s', $name, $extension);

        $storageKey = $file->storeAs(sprintf('workspaces/%s/attachments', $channel->workspace_id), $filename);

        return $message->attachments()->create([
            'workspace_id' => $channel->workspace_id,
            'user_id' => $user->id,
            'original_filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size_bytes' => $file->getSize(),
            'storage_key' => $storageKey,
        ]);
    }
}
