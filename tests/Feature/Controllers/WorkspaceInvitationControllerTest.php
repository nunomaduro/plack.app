<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;
use App\Notifications\WorkspaceInvitationNotification;
use Illuminate\Support\Facades\Notification;
use Inertia\Support\SessionKey;

it('lets an owner invite someone by email', function (): void {
    Notification::fake();

    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->post(route('workspace.invitations.store', $workspace), [
        'email' => 'invitee@example.com',
    ]);

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Invitation sent.'),
            ],
        ]);

    expect($workspace->invitations()->where('email', 'invitee@example.com')->exists())->toBeTrue();

    Notification::assertSentOnDemand(WorkspaceInvitationNotification::class);
});

it('validates the invitation email', function (): void {
    Notification::fake();

    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $this->actingAs($user)->post(route('workspace.invitations.store', $workspace), [
        'email' => 'not-an-email',
    ])->assertSessionHasErrors('email');

    expect($workspace->invitations()->count())->toBe(0);

    Notification::assertNothingSent();
});

it('does not invite users to public workspaces', function (): void {
    Notification::fake();

    $user = User::factory()->create();
    $workspace = Workspace::factory()->public()->for($user, 'owner')->create();

    $this->actingAs($user)->post(route('workspace.invitations.store', $workspace), [
        'email' => 'invitee@example.com',
    ])->assertSessionHasErrors('email');

    expect($workspace->invitations()->count())->toBe(0);

    Notification::assertNothingSent();
});

it('does not invite an existing member', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $member = User::factory()->create();
    $workspace->members()->attach($member);

    $this->actingAs($user)->post(route('workspace.invitations.store', $workspace), [
        'email' => $member->email,
    ])->assertSessionHasErrors('email');

    expect($workspace->invitations()->count())->toBe(0);
});

it('does not invite the owner', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $this->actingAs($user)->post(route('workspace.invitations.store', $workspace), [
        'email' => $user->email,
    ])->assertSessionHasErrors('email');
});

it('does not invite the same email twice', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    WorkspaceInvitation::factory()->for($workspace)->create(['email' => 'invitee@example.com']);

    $this->actingAs($user)->post(route('workspace.invitations.store', $workspace), [
        'email' => 'invitee@example.com',
    ])->assertSessionHasErrors('email');

    expect($workspace->invitations()->count())->toBe(1);
});

it('does not let a non-owner invite', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();

    $this->actingAs($user)->post(route('workspace.invitations.store', $workspace), [
        'email' => 'invitee@example.com',
    ])->assertStatus(404);
});

it('lets an owner cancel an invitation', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $invitation = WorkspaceInvitation::factory()->for($workspace)->create();

    $this->actingAs($user)->delete(route('workspace.invitations.destroy', [$workspace, $invitation]))
        ->assertRedirectBack();

    expect(WorkspaceInvitation::query()->whereKey($invitation->id)->exists())->toBeFalse();
});

it('does not cancel an invitation that belongs to another workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $otherWorkspace = Workspace::factory()->for($user, 'owner')->create();
    $invitation = WorkspaceInvitation::factory()->for($otherWorkspace)->create();

    $this->actingAs($user)->delete(route('workspace.invitations.destroy', [$workspace, $invitation]))
        ->assertStatus(404);

    expect(WorkspaceInvitation::query()->whereKey($invitation->id)->exists())->toBeTrue();
});

it('does not let a non-owner cancel an invitation', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $invitation = WorkspaceInvitation::factory()->for($workspace)->create();

    $this->actingAs($user)->delete(route('workspace.invitations.destroy', [$workspace, $invitation]))
        ->assertStatus(404);
});

it('lets the invited user accept', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $invitation = WorkspaceInvitation::factory()->for($workspace)->create(['email' => $user->email]);

    $this->actingAs($user)->post(route('invitations.accept', $invitation))
        ->assertRedirect(route('workspace.index'));

    expect($workspace->members()->whereKey($user->id)->exists())->toBeTrue()
        ->and(WorkspaceInvitation::query()->whereKey($invitation->id)->exists())->toBeFalse();
});

it('does not let a mismatched user accept', function (): void {
    $user = User::factory()->create();
    $invitation = WorkspaceInvitation::factory()->create();

    $this->actingAs($user)->post(route('invitations.accept', $invitation))
        ->assertStatus(403);

    expect(WorkspaceInvitation::query()->whereKey($invitation->id)->exists())->toBeTrue();
});

it('does not let a user accept an expired invitation', function (): void {
    $user = User::factory()->create();
    $invitation = WorkspaceInvitation::factory()->expired()->create(['email' => $user->email]);

    $this->actingAs($user)->post(route('invitations.accept', $invitation))
        ->assertStatus(403);
});

it('lets the invited user decline', function (): void {
    $user = User::factory()->create();
    $invitation = WorkspaceInvitation::factory()->create(['email' => $user->email]);

    $this->actingAs($user)->delete(route('invitations.decline', $invitation))
        ->assertRedirect(route('workspace.index'));

    expect(WorkspaceInvitation::query()->whereKey($invitation->id)->exists())->toBeFalse();
});

it('does not let a mismatched user decline', function (): void {
    $user = User::factory()->create();
    $invitation = WorkspaceInvitation::factory()->create();

    $this->actingAs($user)->delete(route('invitations.decline', $invitation))
        ->assertStatus(403);

    expect(WorkspaceInvitation::query()->whereKey($invitation->id)->exists())->toBeTrue();
});
