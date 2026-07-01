<?php

declare(strict_types=1);

use App\Actions\DeleteBookmark;
use App\Models\Bookmark;

it('may delete a bookmark', function (): void {
    $bookmark = Bookmark::factory()->create();

    resolve(DeleteBookmark::class)->handle($bookmark);

    expect($bookmark->fresh()->trashed())->toBeTrue();
});
