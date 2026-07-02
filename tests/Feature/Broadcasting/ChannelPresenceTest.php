<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;

beforeEach(function (): void {
    config()->set('broadcasting.default', 'reverb');
    config()->set('broadcasting.connections.reverb.key', 'test-key');
    config()->set('broadcasting.connections.reverb.secret', 'test-secret');
    config()->set('broadcasting.connections.reverb.app_id', 'test-app-id');
    config()->set('broadcasting.connections.reverb.options.host', 'localhost');
    config()->set('broadcasting.connections.reverb.options.port', 8080);
    config()->set('broadcasting.connections.reverb.options.scheme', 'http');

    require base_path('routes/channels.php');
});

it('authorizes a member to join the channel presence channel', function (): void {
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $workspace->members()->attach($member);

    $response = $this->actingAs($member)->post('/broadcasting/auth', [
        'channel_name' => 'presence-channels.'.$channel->id,
        'socket_id' => '123.456',
    ]);

    $response->assertOk()->assertJsonStructure(['auth', 'channel_data']);

    $channelData = json_decode((string) $response->json('channel_data'), true);

    expect($channelData['user_id'])->toBe($member->id)
        ->and($channelData['user_info'])->toBe(['id' => $member->id, 'name' => $member->name]);
});

it('authorizes the owner to join the channel presence channel', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $response = $this->actingAs($user)->post('/broadcasting/auth', [
        'channel_name' => 'presence-channels.'.$channel->id,
        'socket_id' => '123.456',
    ]);

    $response->assertOk();

    $channelData = json_decode((string) $response->json('channel_data'), true);

    expect($channelData['user_info'])->toBe(['id' => $user->id, 'name' => $user->name]);
});

it('still authorizes a member to subscribe to the private channel', function (): void {
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $workspace->members()->attach($member);

    $this->actingAs($member)->post('/broadcasting/auth', [
        'channel_name' => 'private-channels.'.$channel->id,
        'socket_id' => '123.456',
    ])->assertOk()->assertJsonStructure(['auth']);
});

it('denies a user outside the workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post('/broadcasting/auth', [
        'channel_name' => 'presence-channels.'.$channel->id,
        'socket_id' => '123.456',
    ])->assertForbidden();
});

it('denies joining for an unknown channel', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post('/broadcasting/auth', [
        'channel_name' => 'presence-channels.unknown-channel-id',
        'socket_id' => '123.456',
    ])->assertForbidden();
});

it('denies a guest', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->post('/broadcasting/auth', [
        'channel_name' => 'presence-channels.'.$channel->id,
        'socket_id' => '123.456',
    ])->assertForbidden();
});
