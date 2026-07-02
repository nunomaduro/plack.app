<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\JoinWorkspace;
use App\Models\User;
use App\Models\Workspace;
use App\Queries\FindPendingWorkspaceJoin;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

final readonly class WorkspaceJoinController
{
    private const string SessionKey = 'pendingWorkspaceJoin';

    public function show(Request $request, string $joinCode, FindPendingWorkspaceJoin $findPendingWorkspaceJoin): RedirectResponse
    {
        abort_unless($findPendingWorkspaceJoin->get($joinCode) !== null, 404);

        $request->session()->put(self::SessionKey, $joinCode);

        if ($request->user() === null) {
            $request->session()->put('url.intended', route('workspace.join', $joinCode, absolute: false));

            return to_route('login', ['join' => $joinCode]);
        }

        return to_route('workspace.index');
    }

    public function store(
        Request $request,
        string $joinCode,
        #[CurrentUser] User $user,
        FindPendingWorkspaceJoin $findPendingWorkspaceJoin,
        JoinWorkspace $joinWorkspace,
    ): RedirectResponse {
        abort_unless($request->session()->get(self::SessionKey) === $joinCode, 404);

        $workspace = $findPendingWorkspaceJoin->workspace($joinCode);
        abort_unless($workspace instanceof Workspace, 404);

        $joinWorkspace->handle($workspace, $user);
        $request->session()->forget(self::SessionKey);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace joined.'),
        ]);

        return to_route('workspace.index');
    }

    public function destroy(Request $request, string $joinCode): RedirectResponse
    {
        if ($request->session()->get(self::SessionKey) === $joinCode) {
            $request->session()->forget(self::SessionKey);
        }

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Workspace join declined.'),
        ]);

        return to_route('workspace.index');
    }
}
