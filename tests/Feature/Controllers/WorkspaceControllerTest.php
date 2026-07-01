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

it('does not expose sensitive fields when showing a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $this->actingAs($user)->get(route('workspace.show', $workspace))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/show')
            ->where('workspace.id', $workspace->id)
            ->where('workspace.name', $workspace->name)
            ->missing('workspace.user_id')
            ->missing('workspace.created_at')
            ->missing('workspace.updated_at')
        );
});

it('does not expose sensitive fields when listing workspaces', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Test']);

    $this->actingAs($user)->get('workspaces')
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/list')
            ->has('workspaces.data', 1, fn (Assert $item): Assert => $item
                ->where('id', $workspace->id)
                ->where('name', 'Test')
                ->has('created_at')
                ->missing('user_id')
                ->missing('updated_at')
            )
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
        ->and($workspaces->first()->name)->toBe('Test Workspace');
});

it('validates the workspace name', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'ab',
    ])->assertSessionHasErrors('name');

    expect($user->workspaces()->count())->toBe(0);
});

it('cannot create a workspace with a name already used by the user', function (): void {
    $user = User::factory()->create();
    Workspace::factory()->for($user, 'owner')->create(['name' => 'Test Workspace']);

    $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ])->assertSessionHasErrors('name');

    expect($user->workspaces()->count())->toBe(1);
});

it('allows different users to create workspaces with the same name', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    Workspace::factory()->for($otherUser, 'owner')->create(['name' => 'Test Workspace']);

    $this->actingAs($user)->post(route('workspace.store'), [
        'name' => 'Test Workspace',
    ])->assertSessionHasNoErrors();

    expect($user->workspaces()->count())->toBe(1);
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
