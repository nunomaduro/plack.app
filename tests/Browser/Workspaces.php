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

    expect($user->workspaces()->where('name', 'Acme')->exists())->toBeTrue();
});

it('validates the workspace name when creating', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user);

    $page = visit('/workspaces');

    $page->click('@create-workspace-trigger')
        ->fill('name', 'ab')
        ->click('@create-workspace-submit')
        ->assertPresent('@input-error');

    expect($user->workspaces()->count())->toBe(0);
});

it('can update a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['name' => 'Acme']);

    $this->actingAs($user);

    $page = visit('/workspaces');

    $page->click('@edit-workspace-trigger')
        ->fill('name', 'Globex')
        ->click('@edit-workspace-submit')
        ->assertMissing('@edit-workspace-dialog');

    expect($workspace->refresh()->name)->toBe('Globex');
});
