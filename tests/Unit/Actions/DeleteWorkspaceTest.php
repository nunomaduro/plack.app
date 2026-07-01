<?php

declare(strict_types=1);

use App\Actions\DeleteWorkspace;
use App\Models\Workspace;

it('may delete a workspace', function (): void {
    $workspace = Workspace::factory()->create();

    $action = resolve(DeleteWorkspace::class);

    $action->handle($workspace);

    expect(Workspace::query()->whereKey($workspace->id)->exists())->toBeFalse();
});
