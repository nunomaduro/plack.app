<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteReaction;
use App\Http\Requests\DeleteReactionRequest;
use App\Models\Channel;
use App\Models\Reaction;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class DeleteReactionController
{
    public function __invoke(
        DeleteReactionRequest $request,
        Workspace $workspace,
        Channel $channel,
        Reaction $reaction,
        DeleteReaction $deleteReaction,
    ): RedirectResponse {
        $deleteReaction->handle($reaction);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Reaction removed.'),
        ]);

        return back();
    }
}
