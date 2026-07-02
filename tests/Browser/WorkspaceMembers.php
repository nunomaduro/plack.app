<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;

it('can invite a member', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['slug' => 'acme']);

    $this->actingAs($user);

    $page = visit(route('workspace.settings', $workspace));

    $page->click('@invite-member-trigger')
        ->fill('email', 'teammate@example.com')
        ->click('@invite-member-submit')
        ->assertMissing('@invite-member-dialog');

    expect($workspace->invitations()->where('email', 'teammate@example.com')->exists())->toBeTrue();
});

it('can accept a workspace invitation', function (): void {
    $inviter = User::factory()->create();
    $workspace = Workspace::factory()->for($inviter, 'owner')->create(['slug' => 'acme']);
    Channel::factory()->for($workspace)->create();

    $user = User::factory()->create();
    WorkspaceInvitation::factory()->for($workspace)->create(['email' => $user->email]);

    $this->actingAs($user);

    $page = visit('/workspaces');

    $page->click('@accept-invitation')
        ->assertMissing('@accept-invitation');

    expect($workspace->members()->whereKey($user->id)->exists())->toBeTrue();
});
