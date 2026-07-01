<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteBookmark;
use App\Http\Requests\DeleteBookmarkRequest;
use App\Models\Bookmark;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class DeleteBookmarkController
{
    public function __invoke(
        DeleteBookmarkRequest $request,
        Bookmark $bookmark,
        DeleteBookmark $deleteBookmark,
    ): RedirectResponse {
        $deleteBookmark->handle($bookmark);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Bookmark removed.'),
        ]);

        return back();
    }
}
