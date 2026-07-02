<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use Inertia\Testing\AssertableInertia as Assert;

it('exposes no public join url for a private workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->private()->create();

    $this->actingAs($user)->get(route('workspace.settings', $workspace))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/settings')
            ->where('publicJoinUrl', null)
        );
});

it('exposes the public join url for a public workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->public()->create();

    $this->actingAs($user)->get(route('workspace.settings', $workspace))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/settings')
            ->where('publicJoinUrl', route('workspace.join', $workspace->join_code))
        );
});
