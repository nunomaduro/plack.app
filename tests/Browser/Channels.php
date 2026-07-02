<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;

it('can create a channel', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['slug' => 'acme']);
    $channel = Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);

    $this->actingAs($user);

    $page = visit(route('channel.show', [$workspace, $channel]));

    $page->click('@create-channel-trigger')
        ->fill('name', 'Announcements')
        ->click('@create-channel-submit')
        ->assertMissing('@create-channel-dialog');

    expect($workspace->channels()->where('name', 'Announcements')->exists())->toBeTrue();
});

it('validates the channel name when creating', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create(['slug' => 'acme']);
    $channel = Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);

    $this->actingAs($user);

    $page = visit(route('channel.show', [$workspace, $channel]));

    $page->click('@create-channel-trigger')
        ->fill('name', 'ab')
        ->click('@create-channel-submit')
        ->assertPresent('@input-error');

    expect($workspace->channels()->count())->toBe(1);
});
