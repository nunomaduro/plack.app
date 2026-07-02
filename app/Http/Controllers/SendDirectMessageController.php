<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SendDirectMessage;
use App\Http\Requests\StoreDirectMessageMessageRequest;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class SendDirectMessageController
{
    public function __invoke(
        StoreDirectMessageMessageRequest $request,
        #[CurrentUser] User $user,
        Conversation $conversation,
        SendDirectMessage $sendDirectMessage,
    ): RedirectResponse {
        abort_unless($conversation->participants()->whereKey($user->id)->exists(), 404);

        $body = $request->string('body')->value();

        $sendDirectMessage->handle($conversation, $user, $body);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Message sent.'),
        ]);

        return back();
    }
}
