<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateMessage;
use App\Http\Requests\CreateMessageRequest;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;

final readonly class MessageController
{
    public function store(
        CreateMessageRequest $request,
        #[CurrentUser] User $user,
        Workspace $workspace,
        Channel $channel,
        CreateMessage $createMessage,
    ): RedirectResponse {
        $createMessage->handle($channel, $user, $request->string('body')->value());

        return back();
    }
}
