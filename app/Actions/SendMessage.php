<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class SendMessage
{
    public function __construct(
        private CreateAttachment $createAttachment,
    ) {}

    /**
     * @param  array<int, UploadedFile>  $attachments
     */
    public function handle(Channel $channel, User $sender, ?string $body = null, array $attachments = []): Message
    {
        return DB::transaction(function () use ($channel, $sender, $body, $attachments): Message {
            $message = $channel->messages()->create([
                'user_id' => $sender->id,
                'body' => $body,
            ]);

            foreach ($attachments as $file) {
                $this->createAttachment->handle($channel, $message, $sender, $file);
            }

            return $message;
        });
    }
}
