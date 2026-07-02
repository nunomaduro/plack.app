<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateUser;
use App\Actions\DeleteUser;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\DeleteUserRequest;
use App\Models\User;
use App\Queries\FindPendingWorkspaceInvitation;
use App\Queries\FindPendingWorkspaceJoin;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

final readonly class UserController
{
    public function create(
        Request $request,
        FindPendingWorkspaceInvitation $findPendingWorkspaceInvitation,
        FindPendingWorkspaceJoin $findPendingWorkspaceJoin,
    ): Response {
        $workspaceJoin = $findPendingWorkspaceJoin->get($request->query('join') ?? $request->session()->get('pendingWorkspaceJoin'));

        if ($workspaceJoin !== null) {
            $request->session()->put('pendingWorkspaceJoin', $workspaceJoin['code']);
        }

        return Inertia::render('user/create', [
            'workspaceInvitation' => $findPendingWorkspaceInvitation->get($request->query('invitation')),
            'workspaceJoin' => $workspaceJoin,
        ]);
    }

    public function store(CreateUserRequest $request, CreateUser $action): RedirectResponse
    {
        /** @var array<string, mixed> $attributes */
        $attributes = $request->safe()->except('password');

        $user = $action->handle(
            $attributes,
            $request->string('password')->value(),
        );

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(route('workspace.index', absolute: false));
    }

    public function destroy(DeleteUserRequest $request, #[CurrentUser] User $user, DeleteUser $action): RedirectResponse
    {
        Auth::logout();

        $action->handle($user);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('home');
    }
}
