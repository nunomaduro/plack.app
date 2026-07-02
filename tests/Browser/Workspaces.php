<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;

it('can create a private workspace by default', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/workspaces');

    $page->click('@create-workspace-trigger')
        ->fill('name', 'Acme')
        ->click('@create-workspace-submit')
        ->assertMissing('@create-workspace-dialog');

    $workspace = $user->ownedWorkspaces()->where('name', 'Acme')->sole();

    expect($workspace->type->value)->toBe('private')
        ->and($workspace->join_code)->toBeNull();
});

it('can create a public workspace', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/workspaces');

    $page->click('@create-workspace-trigger')
        ->click('@create-workspace-public')
        ->fill('name', 'Public Acme')
        ->click('@create-workspace-submit')
        ->assertMissing('@create-workspace-dialog');

    $workspace = $user->ownedWorkspaces()->where('name', 'Public Acme')->sole();

    expect($workspace->type->value)->toBe('public')
        ->and($workspace->join_code)->toBeString();
});

it('validates the workspace name when creating', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/workspaces');

    $page->click('@create-workspace-trigger')
        ->fill('name', 'ab')
        ->click('@create-workspace-submit')
        ->assertPresent('@input-error');

    expect($user->ownedWorkspaces()->count())->toBe(0);
});

it('can update a workspace from its settings page', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Acme', 'slug' => 'acme']);

    $this->actingAs($user);

    $page = visit(route('workspace.settings', $workspace));

    $page->fill('name', 'Globex')
        ->fill('slug', 'globex')
        ->click('@update-workspace-submit')
        ->wait(0.5);

    expect($workspace->refresh()->name)->toBe('Globex')
        ->and($workspace->slug)->toBe('globex');
});
