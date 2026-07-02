<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\BroadcastTyping;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\Response;

final readonly class ChannelTypingController
{
    public function __invoke(
        #[CurrentUser] User $user,
        Workspace $workspace,
        Channel $channel,
        BroadcastTyping $broadcastTyping,
    ): Response {
        $broadcastTyping->handle($channel, $user);

        return response()->noContent();
    }
}
