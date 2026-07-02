<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;

it('exposes each channel with its id, name, and slug', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->private()->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->get(route('workspace.settings', $workspace))
        ->assertOk()
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/settings')
            ->where('workspace.channels', fn (Collection $channels): bool => $channels->contains(
                fn (array $item): bool => $item['id'] === $channel->id
                    && $item['name'] === $channel->name
                    && $item['slug'] === $channel->slug,
            ))
        );
});

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
