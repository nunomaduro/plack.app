<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use Inertia\Testing\AssertableInertia as Assert;

it('stores a pending public join for an authenticated user', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->create();

    $this->actingAs($user)->get(route('workspace.join', $workspace->join_code))
        ->assertRedirectToRoute('workspace.index')
        ->assertSessionHas('pendingWorkspaceJoin', $workspace->join_code);

    $this->actingAs($user)->get(route('workspace.index'))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('pendingWorkspaceJoin.code', $workspace->join_code)
            ->where('pendingWorkspaceJoin.workspace.id', $workspace->id)
            ->where('pendingWorkspaceJoin.workspace.name', $workspace->name)
        );
});

it('redirects guests through login with pending public join context', function (): void {
    $workspace = Workspace::factory()->public()->create();

    $this->get(route('workspace.join', $workspace->join_code))
        ->assertRedirectToRoute('login', ['join' => $workspace->join_code])
        ->assertSessionHas('pendingWorkspaceJoin', $workspace->join_code);

    $this->get(route('login', ['join' => $workspace->join_code]))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('session/create')
            ->where('workspaceJoin.code', $workspace->join_code)
            ->where('workspaceJoin.workspace.id', $workspace->id)
            ->where('workspaceJoin.workspace.name', $workspace->name)
        );
});

it('rejects invalid private and rotated join codes', function (): void {
    $user = User::factory()->create();
    $privateWorkspace = Workspace::factory()->private()->create([
        'join_code' => 'private-join-code',
    ]);
    $rotatedWorkspace = Workspace::factory()->public()->create([
        'join_code' => 'new-public-join-code',
    ]);

    $this->actingAs($user)->get(route('workspace.join', 'missing-code'))
        ->assertNotFound();

    $this->actingAs($user)->get(route('workspace.join', $privateWorkspace->join_code))
        ->assertNotFound();

    $this->actingAs($user)->get(route('workspace.join', 'old-public-join-code'))
        ->assertNotFound();

    expect($privateWorkspace->members()->whereKey($user->id)->exists())->toBeFalse()
        ->and($rotatedWorkspace->members()->whereKey($user->id)->exists())->toBeFalse();
});

it('accepts a pending public join and clears the pending context', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->create();

    $this->actingAs($user)
        ->withSession(['pendingWorkspaceJoin' => $workspace->join_code])
        ->post(route('workspace-joins.accept', $workspace->join_code))
        ->assertRedirectToRoute('workspace.index')
        ->assertSessionMissing('pendingWorkspaceJoin');

    expect($workspace->members()->whereKey($user->id)->exists())->toBeTrue();
});

it('accepting a pending public join is idempotent for existing members', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->create();
    $workspace->members()->attach($user);

    $this->actingAs($user)
        ->withSession(['pendingWorkspaceJoin' => $workspace->join_code])
        ->post(route('workspace-joins.accept', $workspace->join_code))
        ->assertRedirectToRoute('workspace.index');

    expect($workspace->members()->whereKey($user->id)->count())->toBe(1);
});

it('declines a pending public join without attaching the user', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->create();

    $this->actingAs($user)
        ->withSession(['pendingWorkspaceJoin' => $workspace->join_code])
        ->delete(route('workspace-joins.decline', $workspace->join_code))
        ->assertRedirectToRoute('workspace.index')
        ->assertSessionMissing('pendingWorkspaceJoin');

    expect($workspace->members()->whereKey($user->id)->exists())->toBeFalse();
});

it('does not accept a public join without pending context', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->create();

    $this->actingAs($user)
        ->post(route('workspace-joins.accept', $workspace->join_code))
        ->assertNotFound();

    expect($workspace->members()->whereKey($user->id)->exists())->toBeFalse();
});
