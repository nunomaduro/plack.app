<?php

declare(strict_types=1);

use App\Actions\UpdateWorkspace;
use App\Models\Workspace;

it('may update a workspace name', function (): void {
    $workspace = Workspace::factory()->create([
        'name' => 'Subscribed to Channel',
    ]);

    $action = resolve(UpdateWorkspace::class);

    $action->handle($workspace, 'Nuno');

    expect($workspace->refresh()->name)->toBe('Nuno');
});
