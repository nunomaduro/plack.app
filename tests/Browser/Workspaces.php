<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;

it('can create a workspace', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/workspaces');

    $page->click('@create-workspace-trigger')
        ->fill('name', 'Acme')
        ->click('@create-workspace-submit')
        ->assertMissing('@create-workspace-dialog');

    expect($user->ownedWorkspaces()->where('name', 'Acme')->exists())->toBeTrue();
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
        ->click('@update-workspace-submit');

    expect($workspace->refresh()->name)->toBe('Globex')
        ->and($workspace->slug)->toBe('globex');
});
