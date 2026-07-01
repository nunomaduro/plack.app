<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;

it('lets the owner enter the workspace channels', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $this->actingAs($user)
        ->get(route('workspace.channels', $workspace))
        ->assertStatus(200);
});

it('lets an accepted member enter the workspace channels', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();

    $workspace->members()->attach($user);

    $this->actingAs($user)
        ->get(route('workspace.channels', $workspace))
        ->assertStatus(200);
});

it('does not let a stranger enter the workspace channels', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();

    $this->actingAs($user)
        ->get(route('workspace.channels', $workspace))
        ->assertStatus(404);
});

it('redirects guests to login', function (): void {
    $workspace = Workspace::factory()->create();

    $this->get(route('workspace.channels', $workspace))
        ->assertRedirect(route('login'));
});
