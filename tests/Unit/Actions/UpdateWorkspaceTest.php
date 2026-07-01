<?php

declare(strict_types=1);

use App\Actions\UpdateWorkspace;
use App\Models\Workspace;

it('may update a workspace name and slug', function (): void {
    $workspace = Workspace::factory()->create([
        'name' => 'Subscribed to Channel',
        'slug' => 'subscribed-to-channel',
    ]);

    $action = resolve(UpdateWorkspace::class);

    $action->handle($workspace, 'Nuno', 'nuno');

    expect($workspace->refresh())
        ->name->toBe('Nuno')
        ->slug->toBe('nuno');
});
