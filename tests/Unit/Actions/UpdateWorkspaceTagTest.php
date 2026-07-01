<?php

declare(strict_types=1);

use App\Actions\UpdateWorkspaceTag;
use App\Models\WorkspaceTag;

it('may update a workspace tag name', function (): void {
    $tag = WorkspaceTag::factory()->create([
        'name' => 'engineering',
    ]);

    resolve(UpdateWorkspaceTag::class)->handle($tag, 'design');

    expect($tag->refresh()->name)->toBe('design');
});
