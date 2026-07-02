<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;

it('can open the sidebar drawer and switch channels on mobile', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['slug' => 'acme']);
    $general = Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);
    Channel::factory()->for($workspace)->create(['name' => 'random', 'slug' => 'random']);

    $this->actingAs($user);

    $page = visit(route('channel.show', [$workspace, $general]))->on()->mobile();

    $page->assertMissing('@mobile-sidebar')
        ->click('@mobile-sidebar-toggle')
        ->assertPresent('@mobile-sidebar')
        ->click('@mobile-channel-random')
        ->assertRoute('channel.show', ['workspace' => 'acme', 'channel' => 'random'])
        ->assertMissing('@mobile-sidebar');
});

it('hides the mobile sidebar toggle on desktop', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['slug' => 'acme']);
    $channel = Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);

    $this->actingAs($user);

    $page = visit(route('channel.show', [$workspace, $channel]));

    $page->assertVisible('@desktop-channel-general');
});
