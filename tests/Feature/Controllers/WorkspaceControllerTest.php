<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use Inertia\Support\SessionKey;
use Inertia\Testing\AssertableInertia as Assert;

it('redirects to the first workspace when the user owns one', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $this->actingAs($user)->get('workspaces')
        ->assertRedirectToRoute('workspace.show', $workspace);
});

it('redirects to an invited workspace when the user owns none', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->members()->attach($user);

    $this->actingAs($user)->get('workspaces')
        ->assertRedirectToRoute('workspace.show', $workspace);
});

it('shows an empty state when the user has no workspaces', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->get('workspaces')
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/empty')
        );
});

it('redirects a workspace to its first channel', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->get(route('workspace.show', $workspace))
        ->assertRedirectToRoute('channel.show', [$workspace, $channel]);
});

it('shows the workspace settings page', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane']);
    Channel::factory()->for($workspace)->create();

    $workspace->members()->attach(User::factory()->create());
    WorkspaceInvitation::factory()->for($workspace)->create(['email' => 'pending@example.com']);

    $this->actingAs($user)->get(route('workspace.settings', $workspace))
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

it('does not let a non-owner open the workspace settings page', function (): void {
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->members()->attach($member);

    $this->actingAs($member)->get(route('workspace.settings', $workspace))
        ->assertNotFound();
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
        ->and($workspaces->first()->name)->toBe('Test Workspace')
        ->and($workspaces->first()->slug)->toBe('test-workspace');
});

it('validates the workspace name', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'ab',
    ]);

    $response->assertSessionHasErrors('name');

    expect($user->ownedWorkspaces()->count())->toBe(0);
});

it('rejects a workspace name already owned by the same user', function (): void {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->create(['name' => 'Test Workspace']);

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ]);

    $response->assertSessionHasErrors('name');

    expect($user->ownedWorkspaces()->where('name', 'Test Workspace')->count())->toBe(1);
});

it('allows different users to have the same workspace name', function (): void {
    $otherUser = User::factory()->create();
    Workspace::factory()->for($otherUser, 'owner')->create(['name' => 'Test Workspace']);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ]);

    $response->assertSessionHasNoErrors();

    expect($user->ownedWorkspaces()->where('name', 'Test Workspace')->count())->toBe(1);
});

it('generates a unique slug when the name is already taken', function (): void {
    $otherUser = User::factory()->create();
    Workspace::factory()->for($otherUser, 'owner')->create(['name' => 'Test Workspace', 'slug' => 'test-workspace']);

    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ]);

    $response->assertSessionHasNoErrors();

    expect($user->ownedWorkspaces()->where('slug', 'test-workspace-2')->exists())->toBeTrue();
});

it('can update workspace name', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane', 'slug' => 'hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Nuno Maduro',
        'slug' => 'hashane',
    ]);

    $response->assertRedirectBack();

    expect($workspace->refresh()->name)->toBe('Nuno Maduro');
});

it('keeps the slug when the submitted slug is unchanged', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane', 'slug' => 'hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Nuno Maduro',
        'slug' => 'hashane',
    ]);

    $response->assertSessionHasNoErrors();

    expect($workspace->refresh()->slug)->toBe('hashane');
});

it('requires a slug when updating a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane', 'slug' => 'hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Nuno Maduro',
    ]);

    $response->assertSessionHasErrors('slug');

    expect($workspace->refresh()->slug)->toBe('hashane');
});

it('updates the slug when one is provided', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane', 'slug' => 'hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Nuno Maduro',
        'slug' => 'nuno-maduro',
    ]);

    $response->assertRedirectBack()->assertSessionHasNoErrors();

    expect($workspace->refresh()->slug)->toBe('nuno-maduro');
});

it('validates the slug format when updating a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane', 'slug' => 'hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Hashane',
        'slug' => 'Not A Slug',
    ]);

    $response->assertSessionHasErrors('slug');

    expect($workspace->refresh()->slug)->toBe('hashane');
});

it('rejects a slug already taken by another workspace', function (): void {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->create(['name' => 'Taken', 'slug' => 'taken']);
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane', 'slug' => 'hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Hashane',
        'slug' => 'taken',
    ]);

    $response->assertSessionHasErrors('slug');

    expect($workspace->refresh()->slug)->toBe('hashane');
});

it('rejects updating a workspace to a name already owned by the same user', function (): void {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->create(['name' => 'Taken', 'slug' => 'taken']);
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane', 'slug' => 'hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Taken',
        'slug' => 'hashane',
    ]);

    $response->assertSessionHasErrors('name');

    expect($workspace->refresh()->name)->toBe('Hashane');
});

it('allows updating a workspace while keeping its own name', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane', 'slug' => 'hashane']);

    $response = $this->actingAs($user)->patch(route('workspace.update', $workspace), [
        'name' => 'Hashane',
        'slug' => 'nuno-maduro',
    ]);

    $response->assertRedirectBack()->assertSessionHasNoErrors();

    expect($workspace->refresh()->slug)->toBe('nuno-maduro');
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
