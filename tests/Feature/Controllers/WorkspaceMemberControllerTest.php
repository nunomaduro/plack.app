<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;

it('lets an owner remove a member', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $member = User::factory()->create();
    $workspace->members()->attach($member);

    $this->actingAs($user)->delete(route('workspace.members.destroy', [$workspace, $member]))
        ->assertRedirectBack();

    expect($workspace->members()->whereKey($member->id)->exists())->toBeFalse();
});

it('does not let an owner remove themselves', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $this->actingAs($user)->delete(route('workspace.members.destroy', [$workspace, $user]))
        ->assertStatus(403);
});

it('does not let a non-owner remove a member', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $member = User::factory()->create();
    $workspace->members()->attach($member);

    $this->actingAs($user)->delete(route('workspace.members.destroy', [$workspace, $member]))
        ->assertStatus(404);
});
