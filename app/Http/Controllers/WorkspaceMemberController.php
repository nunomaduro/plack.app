<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\RemoveWorkspaceMember;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class WorkspaceMemberController
{
    public function destroy(Workspace $workspace, User $user, RemoveWorkspaceMember $removeWorkspaceMember): RedirectResponse
    {
        abort_if($workspace->user_id === $user->id, 403);

        $removeWorkspaceMember->handle($workspace, $user);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Member removed.'),
        ]);

        return back();
    }
}
