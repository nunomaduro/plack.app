<?php

declare(strict_types=1);

use App\Actions\DeleteWorkspaceTag;
use App\Models\WorkspaceTag;

it('may delete a workspace tag', function (): void {
    $tag = WorkspaceTag::factory()->create();

    resolve(DeleteWorkspaceTag::class)->handle($tag);

    expect(WorkspaceTag::query()->whereKey($tag->id)->exists())->toBeFalse();
});
