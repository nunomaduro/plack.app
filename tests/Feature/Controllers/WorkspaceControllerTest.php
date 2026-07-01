<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use Inertia\Support\SessionKey;
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

it('can show a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Hashane']);

    $this->actingAs($user)->get(route('workspace.show', $workspace))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/show')
            ->where('workspace.id', $workspace->id)
            ->where('workspace.name', 'Hashane')
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

    $workspaces = $user->workspaces;

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

    expect($user->workspaces()->count())->toBe(0);
});

it('allows workspaces to share the same name', function (): void {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->create(['name' => 'Test Workspace']);

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ]);

    $response->assertSessionHasNoErrors();

    expect($user->workspaces()->where('name', 'Test Workspace')->count())->toBe(2);
});

it('generates a unique slug when the name is already taken', function (): void {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->create(['name' => 'Test Workspace', 'slug' => 'test-workspace']);

    $response = $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ]);

    $response->assertSessionHasNoErrors();

    expect($user->workspaces()->where('slug', 'test-workspace-2')->exists())->toBeTrue();
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

it('can delete a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->delete(route('workspace.destroy', $workspace));

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Workspace deleted.'),
            ],
        ]);

    expect($user->workspaces()->count())->toBe(0);
});

it('cannot delete a workspace owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($otherUser, 'owner')->create();

    $this->actingAs($user)->delete(route('workspace.destroy', $workspace))
        ->assertNotFound();

    expect($otherUser->workspaces()->count())->toBe(1);
});
