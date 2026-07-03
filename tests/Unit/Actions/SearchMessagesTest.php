<?php

declare(strict_types=1);

use App\Actions\SearchMessages;
use App\Models\Channel;
use App\Models\Message;
use App\Models\Workspace;

it('finds messages matching the query', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    Message::factory()->for($channel)->create(['body' => 'Hello world']);
    Message::factory()->for($channel)->create(['body' => 'Goodbye world']);

    $results = resolve(SearchMessages::class)->handle($workspace, 'Hello');

    expect($results)->toHaveCount(1)
        ->and($results->first()->body)->toBe('Hello world');
});

it('scopes results to a specific channel', function (): void {
    $workspace = Workspace::factory()->create();
    $channel1 = Channel::factory()->for($workspace)->create();
    $channel2 = Channel::factory()->for($workspace)->create();
    Message::factory()->for($channel1)->create(['body' => 'Hello from channel 1']);
    Message::factory()->for($channel2)->create(['body' => 'Hello from channel 2']);

    $results = resolve(SearchMessages::class)->handle($workspace, 'Hello', $channel1);

    expect($results)->toHaveCount(1)
        ->and($results->first()->body)->toBe('Hello from channel 1');
});

it('searches across all workspace channels when no channel is provided', function (): void {
    $workspace = Workspace::factory()->create();
    $channel1 = Channel::factory()->for($workspace)->create();
    $channel2 = Channel::factory()->for($workspace)->create();
    Message::factory()->for($channel1)->create(['body' => 'Hello from channel 1']);
    Message::factory()->for($channel2)->create(['body' => 'Hello from channel 2']);

    $results = resolve(SearchMessages::class)->handle($workspace, 'Hello');

    expect($results)->toHaveCount(2);
});

it('does not return messages from other workspaces', function (): void {
    $workspace = Workspace::factory()->create();
    $otherWorkspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $otherChannel = Channel::factory()->for($otherWorkspace)->create();
    Message::factory()->for($channel)->create(['body' => 'Hello']);
    Message::factory()->for($otherChannel)->create(['body' => 'Hello']);

    $results = resolve(SearchMessages::class)->handle($workspace, 'Hello');

    expect($results)->toHaveCount(1);
});

it('returns empty results when no messages match', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    Message::factory()->for($channel)->create(['body' => 'Hello world']);

    $results = resolve(SearchMessages::class)->handle($workspace, 'nonexistent');

    expect($results)->toHaveCount(0);
});

it('eager loads sender and channel relationships', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    Message::factory()->for($channel)->create(['body' => 'Hello world']);

    $results = resolve(SearchMessages::class)->handle($workspace, 'Hello');

    expect($results->first()->relationLoaded('sender'))->toBeTrue()
        ->and($results->first()->relationLoaded('channel'))->toBeTrue();
});

it('returns results ordered by newest first', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $older = Message::factory()->for($channel)->create(['body' => 'Hello older', 'created_at' => now()->subDay()]);
    $newer = Message::factory()->for($channel)->create(['body' => 'Hello newer', 'created_at' => now()]);

    $results = resolve(SearchMessages::class)->handle($workspace, 'Hello');

    expect($results->first()->id)->toBe($newer->id)
        ->and($results->last()->id)->toBe($older->id);
});
