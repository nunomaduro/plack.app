<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Inertia\Support\SessionKey;
use Inertia\Testing\AssertableInertia as Assert;

it('splits owned, member, and pending workspaces', function (): void {
    $user = User::factory()->create();

    Workspace::factory()->count(2)->for($user, 'owner')->create();

    $memberWorkspace = Workspace::factory()->create();
    $memberWorkspace->members()->attach($user);

    $pending = WorkspaceInvitation::factory()->create(['email' => $user->email]);
    WorkspaceInvitation::factory()->expired()->create(['email' => $user->email]);

    $this->actingAs($user)->get('workspaces')
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/list')
            ->has('ownedWorkspaces.data', 2)
            ->has('memberWorkspaces', 1)
            ->has('pendingInvitations', 1)
            ->where('pendingInvitations.0.code', $pending->code)
        );
});

it('shows the workspace settings page', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane']);

    $workspace->members()->attach(User::factory()->create());
    WorkspaceInvitation::factory()->for($workspace)->create(['email' => 'pending@example.com']);

    $this->actingAs($user)->get(route('workspace.show', $workspace))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/settings')
            ->where('workspace.id', $workspace->id)
            ->where('workspace.name', 'Hashane')
            ->where('owner.id', $user->id)
            ->has('members', 1)
            ->has('invitations', 1)
        );
});

it('can create workspace', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ]);

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Workspace created.'),
            ],
        ]);

    $workspaces = $user->ownedWorkspaces;

    expect($workspaces->count())->toBe(1)
        ->and($workspaces->first()->name)->toBe('Test Workspace');
});

it('validates the workspace name', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'ab',
    ])->assertSessionHasErrors('name');

    expect($user->ownedWorkspaces()->count())->toBe(0);
});

it('cannot create a workspace with a name already used by the user', function (): void {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->create(['name' => 'Test Workspace']);

    $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ])->assertSessionHasErrors('name');

    expect($user->ownedWorkspaces()->count())->toBe(1);
});

it('allows different users to create workspaces with the same name', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Workspace::factory()->for($otherUser, 'owner')->create(['name' => 'Test Workspace']);

    $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ])->assertSessionHasNoErrors();

    expect($user->ownedWorkspaces()->count())->toBe(1);
});

it('can update workspace name', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Nuno Maduro',
    ]);

    $response->assertRedirectBack();

    expect($workspace->refresh()->name)->toBe('Nuno Maduro');
});

it('can delete a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->delete(route('workspace.destroy', $workspace));

    $response->assertRedirect(route('workspace.index'))
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Workspace deleted.'),
            ],
        ]);

    expect(Workspace::query()->whereKey($workspace->id)->exists())->toBeFalse();
});

it('can not delete a workspace if not owner', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();

    $this->actingAs($user)->delete(route('workspace.destroy', $workspace))
        ->assertStatus(404);

    expect(Workspace::query()->whereKey($workspace->id)->exists())->toBeTrue();
});
