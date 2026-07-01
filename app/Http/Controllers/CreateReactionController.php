<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateReaction;
use App\Enums\Emoji;
use App\Http\Requests\CreateReactionRequest;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class CreateReactionController
{
    public function __invoke(
        CreateReactionRequest $request,
        Workspace $workspace,
        Channel $channel,
        #[CurrentUser] User $user,
        CreateReaction $createReaction,
    ): RedirectResponse {
        $emoji = Emoji::from($request->string('emoji')->value());

        $createReaction->handle($user, $channel, $emoji);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Reaction added.'),
        ]);

        return back();
    }
}
