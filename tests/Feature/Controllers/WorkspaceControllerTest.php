<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use Inertia\Testing\AssertableInertia as Assert;

it('may have workspaces', function (): void {
    $user = User::factory()->create();

    Workspace::factory()
        ->count(5)
        ->for($user, 'owner')
        ->create();

    $this->actingAs($user)->get('workspaces')
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/list')
            ->has('workspaces.data', 5)
        );
});

it('can create workspace', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ]);

    $response->assertRedirectBack();

    $workspaces = $user->workspaces;

    expect($workspaces->count())->toBe(1)
        ->and($workspaces->first()->name)->toBe('Test Workspace');
});
