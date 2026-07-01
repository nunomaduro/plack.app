<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;

it('can create workspace', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ]);

    $response->assertRedirectBack();

    $workspaces = $user->workspaces;

    expect($workspaces->count())->toBe(1)
        ->and($workspaces->first()->name)->toBe('Test Workspace');
});

it('can update workspace name', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user)->create(['name' => 'Hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Nuno Maduro',
    ]);

    $response->assertRedirectBack();

    expect($workspace->refresh()->name)->toBe('Nuno Maduro');
});
