<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\Message;
use App\Models\User;
use App\Models\UserChannelMetadata;
use App\Models\Workspace;
use Illuminate\Support\Collection;
use Inertia\Testing\AssertableInertia as Assert;

/**
 * Resolve the unread count of a channel from the sidebar prop.
 *
 * @param  Collection<int, array{slug: string, unread_count: int}>  $channels
 */
function unreadFor(Collection $channels, string $slug): int
{
    return (int) $channels->firstWhere('slug', $slug)['unread_count'];
}

it('creates channel metadata the first time a channel is viewed', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->get(route('channel.show', [$workspace, $channel]))
        ->assertStatus(200);

    $this->assertDatabaseHas('user_channel_metadata', [
        'channel_id' => $channel->id,
        'user_id' => $user->id,
    ]);

    $metadata = UserChannelMetadata::query()
        ->where('channel_id', $channel->id)
        ->where('user_id', $user->id)
        ->first();

    expect($metadata?->last_read_at)->not->toBeNull();
});

it('counts messages from others in never-opened channels as unread', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $general = Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);
    $random = Channel::factory()->for($workspace)->create(['name' => 'random', 'slug' => 'random']);

    $other = User::factory()->create();
    Message::factory()->count(3)->for($random)->for($other, 'sender')
        ->create(['created_at' => now()->addMinute()]);

    $this->actingAs($user)->get(route('channel.show', [$workspace, $general]))
        ->assertInertia(fn (Assert $page): Assert => $page
            ->has('workspace.channels', 2)
            ->where('workspace.channels', function (Collection $channels): bool {
                expect(unreadFor($channels, 'random'))->toBe(3)
                    ->and(unreadFor($channels, 'general'))->toBe(0);

                return true;
            })
        );
});

it('excludes the viewer own messages from the unread count', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $general = Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);
    $random = Channel::factory()->for($workspace)->create(['name' => 'random', 'slug' => 'random']);

    $other = User::factory()->create();
    Message::factory()->count(2)->for($random)->for($other, 'sender')
        ->create(['created_at' => now()->addMinute()]);
    Message::factory()->for($random)->for($user, 'sender')
        ->create(['created_at' => now()->addMinute()]);

    $this->actingAs($user)->get(route('channel.show', [$workspace, $general]))
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('workspace.channels', function (Collection $channels): bool {
                expect(unreadFor($channels, 'random'))->toBe(2);

                return true;
            })
        );
});

it('clears the unread count once a channel is opened', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create(['name' => 'random', 'slug' => 'random']);

    $other = User::factory()->create();
    Message::factory()->count(2)->for($channel)->for($other, 'sender')->create();

    $this->actingAs($user)->get(route('channel.show', [$workspace, $channel]))
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('workspace.channels', function (Collection $channels): bool {
                expect(unreadFor($channels, 'random'))->toBe(0);

                return true;
            })
        );
});

it('only counts messages sent after a member joined the workspace', function (): void {
    $owner = User::factory()->create();
    $workspace = Workspace::factory()->for($owner, 'owner')->create();
    $general = Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);
    $random = Channel::factory()->for($workspace)->create(['name' => 'random', 'slug' => 'random']);

    $joinedAt = now();

    // Messages sent before the member joined — must not count as unread.
    Message::factory()->count(2)->for($random)->for($owner, 'sender')
        ->create(['created_at' => $joinedAt->copy()->subHour()]);

    $member = User::factory()->create();
    $workspace->members()->attach($member, [
        'created_at' => $joinedAt,
        'updated_at' => $joinedAt,
    ]);

    // Messages sent after the member joined — must count as unread.
    Message::factory()->count(3)->for($random)->for($owner, 'sender')
        ->create(['created_at' => $joinedAt->copy()->addHour()]);

    $this->actingAs($member)->get(route('channel.show', [$workspace, $general]))
        ->assertInertia(fn (Assert $page): Assert => $page
            ->where('workspace.channels', function (Collection $channels): bool {
                expect(unreadFor($channels, 'random'))->toBe(3);

                return true;
            })
        );
});
