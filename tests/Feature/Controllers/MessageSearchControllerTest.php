<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;

it('returns search results as json', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();
    Message::factory()->for($channel)->create(['body' => 'Hello world']);

    $response = $this->actingAs($user)->getJson(route('message.search', [
        'workspace' => $workspace,
        'query' => 'Hello',
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.body', 'Hello world');
});

it('validates the query parameter is required', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->getJson(route('message.search', [
        'workspace' => $workspace,
    ]));

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('query');
});

it('filters results by channel when channel_id is provided', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel1 = Channel::factory()->for($workspace)->create();
    $channel2 = Channel::factory()->for($workspace)->create();
    Message::factory()->for($channel1)->create(['body' => 'Hello from channel 1']);
    Message::factory()->for($channel2)->create(['body' => 'Hello from channel 2']);

    $response = $this->actingAs($user)->getJson(route('message.search', [
        'workspace' => $workspace,
        'query' => 'Hello',
        'channel_id' => $channel1->id,
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.body', 'Hello from channel 1');
});

it('returns 404 when channel does not belong to workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $otherChannel = Channel::factory()->create();

    $response = $this->actingAs($user)->getJson(route('message.search', [
        'workspace' => $workspace,
        'query' => 'Hello',
        'channel_id' => $otherChannel->id,
    ]));

    $response->assertNotFound();
});

it('does not return messages from other workspaces', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $otherWorkspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $otherChannel = Channel::factory()->for($otherWorkspace)->create();
    Message::factory()->for($channel)->create(['body' => 'Hello']);
    Message::factory()->for($otherChannel)->create(['body' => 'Hello']);

    $response = $this->actingAs($user)->getJson(route('message.search', [
        'workspace' => $workspace,
        'query' => 'Hello',
    ]));

    $response->assertOk()
        ->assertJsonCount(1, 'data');
});

it('requires authentication', function (): void {
    $workspace = Workspace::factory()->create();

    $response = $this->getJson(route('message.search', [
        'workspace' => $workspace,
        'query' => 'Hello',
    ]));

    $response->assertUnauthorized();
});

it('returns 404 for non-owner workspace access', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();

    $response = $this->actingAs($user)->getJson(route('message.search', [
        'workspace' => $workspace,
        'query' => 'Hello',
    ]));

    $response->assertNotFound();
});
