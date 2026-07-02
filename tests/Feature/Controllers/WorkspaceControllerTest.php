<?php

declare(strict_types=1);

use App\Enums\WorkspaceType;
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

it('shows invitations and no public join url for private workspace settings', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->private()->for($user, 'owner')->create();
    Channel::factory()->for($workspace)->create();
    WorkspaceInvitation::factory()->for($workspace)->create(['email' => 'pending@example.com']);

    $this->actingAs($user)->get(route('workspace.settings', $workspace))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/settings')
            ->where('workspace.type', WorkspaceType::Private->value)
            ->where('publicJoinUrl', null)
            ->has('invitations', 1)
        );
});

it('shows a public join url and no invitations for public workspace settings', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->for($user, 'owner')->create();
    Channel::factory()->for($workspace)->create();
    WorkspaceInvitation::factory()->for($workspace)->create(['email' => 'pending@example.com']);

    $this->actingAs($user)->get(route('workspace.settings', $workspace))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/settings')
            ->where('workspace.type', WorkspaceType::Public->value)
            ->where('publicJoinUrl', route('workspace.join', $workspace->join_code))
            ->where('invitations', [])
        );
});

it('does not show a public join url when a public workspace has no join code', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->for($user, 'owner')->create([
        'join_code' => null,
    ]);
    Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->get(route('workspace.settings', $workspace))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/settings')
            ->where('workspace.type', WorkspaceType::Public->value)
            ->where('publicJoinUrl', null)
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
        ->and($workspaces->first()->slug)->toBe('test-workspace')
        ->and($workspaces->first()->type)->toBe(WorkspaceType::Private)
        ->and($workspaces->first()->join_code)->toBeNull();
});

it('can create a public workspace', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Public Workspace',
        'type' => WorkspaceType::Public->value,
    ]);

    $response->assertRedirectBack()->assertSessionHasNoErrors();

    $workspace = $user->ownedWorkspaces()->sole();

    expect($workspace->type)->toBe(WorkspaceType::Public)
        ->and($workspace->join_code)->toBeString()
        ->and($workspace->join_code)->toHaveLength(64);
});

it('rejects invalid workspace type values', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
        'type' => 'shared',
    ]);

    $response->assertSessionHasErrors('type');

    expect($user->ownedWorkspaces()->count())->toBe(0);
});

it('lets an owner regenerate a public workspace join link', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->for($user, 'owner')->create([
        'join_code' => 'old-public-join-code',
    ]);

    $this->actingAs($user)->post(route('workspace.join-link.regenerate', $workspace))
        ->assertRedirectBack();

    expect($workspace->refresh()->join_code)->not->toBe('old-public-join-code')
        ->and($workspace->join_code)->toBeString()
        ->and($workspace->join_code)->toHaveLength(64);
});

it('does not let a non-owner regenerate a public workspace join link', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->create([
        'join_code' => 'public-join-code',
    ]);

    $this->actingAs($user)->post(route('workspace.join-link.regenerate', $workspace))
        ->assertNotFound();

    expect($workspace->refresh()->join_code)->toBe('public-join-code');
});

it('does not regenerate a private workspace join link', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->private()->for($user, 'owner')->create();

    $this->actingAs($user)->post(route('workspace.join-link.regenerate', $workspace))
        ->assertNotFound();

    expect($workspace->refresh()->join_code)->toBeNull();
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
