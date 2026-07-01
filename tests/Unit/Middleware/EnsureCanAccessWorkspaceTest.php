<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;

it('can access the workspace', function (): void {
    $user = User::factory()->create();

    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)
        ->get(route('workspace.show', $workspace));

    $response
        ->assertStatus(200);
});

it('can not access workspace if not owner', function (): void {
    $user = User::factory()->create();

    $workspace = Workspace::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('workspace.show', $workspace));

    $response
        ->assertStatus(404);
});
