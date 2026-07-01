<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateUserTag;
use App\Actions\DeleteUserTag;
use App\Actions\UpdateUserTag;
use App\Http\Requests\CreateUserTagRequest;
use App\Http\Requests\DeleteUserTagRequest;
use App\Http\Requests\UpdateUserTagRequest;
use App\Models\UserTag;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class UserTagController
{
    public function index(Workspace $workspace): JsonResponse
    {
        return response()->json($workspace->userTags);
    }

    public function store(
        CreateUserTagRequest $request,
        Workspace $workspace,
        CreateUserTag $createUserTag,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $createUserTag->handle($workspace, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User tag created.'),
        ]);

        return back();
    }

    public function update(
        UpdateUserTagRequest $request,
        Workspace $workspace,
        UserTag $userTag,
        UpdateUserTag $updateUserTag,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $updateUserTag->handle($userTag, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User tag updated.'),
        ]);

        return back();
    }

    public function destroy(
        DeleteUserTagRequest $request,
        Workspace $workspace,
        UserTag $userTag,
        DeleteUserTag $deleteUserTag,
    ): RedirectResponse {
        $deleteUserTag->handle($userTag);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('User tag deleted.'),
        ]);

        return back();
    }
}
