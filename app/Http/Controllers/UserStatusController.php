<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\UpdateUserStatus;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class UserStatusController
{
    public function update(
        UpdateUserStatusRequest $request,
        #[CurrentUser] User $user,
        UpdateUserStatus $updateUserStatus,
    ): RedirectResponse {
        $updateUserStatus->handle(
            $user,
            $request->string('emoji')->value() ?: null,
            $request->string('text')->value() ?: null,
            $request->date('expires_at'),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Status updated.'),
        ]);

        return back();
    }
}
