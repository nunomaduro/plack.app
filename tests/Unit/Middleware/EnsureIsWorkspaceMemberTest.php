<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;

it('lets the owner enter the workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)
        ->get(route('workspace.show', $workspace))
        ->assertRedirectToRoute('channel.show', [$workspace, $channel]);
});

it('lets an accepted member enter the workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();

    $workspace->members()->attach($user);

    $this->actingAs($user)
        ->get(route('workspace.show', $workspace))
        ->assertRedirectToRoute('channel.show', [$workspace, $channel]);
});

it('does not let a stranger enter the workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();

    $this->actingAs($user)
        ->get(route('workspace.show', $workspace))
        ->assertStatus(404);
});

it('redirects guests to login', function (): void {
    $workspace = Workspace::factory()->create();

    $this->get(route('workspace.show', $workspace))
        ->assertRedirect(route('login'));
});
