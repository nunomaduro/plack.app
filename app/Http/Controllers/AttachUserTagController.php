<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\AttachUserTag;
use App\Http\Requests\AttachUserTagRequest;
use App\Models\User;
use App\Models\UserTag;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class AttachUserTagController
{
    public function __invoke(
        AttachUserTagRequest $request,
        Workspace $workspace,
        UserTag $userTag,
        User $user,
        AttachUserTag $attachUserTag,
    ): RedirectResponse {
        $attachUserTag->handle($user, $userTag);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User tag attached.'),
        ]);

        return back();
    }
}
