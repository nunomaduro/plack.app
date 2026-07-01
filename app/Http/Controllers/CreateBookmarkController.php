<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateBookmark;
use App\Http\Requests\CreateBookmarkRequest;
use App\Models\Message;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class CreateBookmarkController
{
    public function __invoke(
        CreateBookmarkRequest $request,
        #[CurrentUser] User $user,
        CreateBookmark $createBookmark,
    ): RedirectResponse {
        $message = Message::query()->findOrFail($request->string('message_id')->value());

        $createBookmark->handle($user, $message);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Message bookmarked.'),
        ]);

        return back();
    }
}
