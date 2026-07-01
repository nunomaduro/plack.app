<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DetachUserTag;
use App\Http\Requests\DetachUserTagRequest;
use App\Models\User;
use App\Models\UserTag;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class DetachUserTagController
{
    public function __invoke(
        DetachUserTagRequest $request,
        Workspace $workspace,
        UserTag $userTag,
        User $user,
        DetachUserTag $detachUserTag,
    ): RedirectResponse {
        $detachUserTag->handle($user, $userTag);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User tag detached.'),
        ]);

        return back();
    }
}
