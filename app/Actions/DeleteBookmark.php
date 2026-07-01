<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Bookmark;

final readonly class DeleteBookmark
{
    public function handle(Bookmark $bookmark): void
    {
        $bookmark->delete();
    }
}
