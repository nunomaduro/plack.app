<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\Message;
use Illuminate\Support\Facades\DB;

final readonly class DeleteChannel
{
    public function __construct(private DeleteMessage $deleteMessage)
    {
        //
    }

    public function handle(Channel $channel): void
    {
        DB::transaction(function () use ($channel): void {
            $channel->messages()->each(fn (Message $message) => $this->deleteMessage->handle($message));

            $channel->delete();
        });
    }
}
