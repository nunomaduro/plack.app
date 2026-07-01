<?php

declare(strict_types=1);

use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Inertia\Support\SessionKey;
use Inertia\Testing\AssertableInertia as Assert;

it('lists channels on the workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    Channel::factory()
        ->count(3)
        ->for($workspace)
        ->create();

    $this->actingAs($user)->get(route('workspace.show', $workspace))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('workspace/show')
            ->has('workspace.channels', 3)
        );
});

it('can show a channel', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create(['name' => 'general']);

    $this->actingAs($user)->get(route('channel.show', [$workspace, $channel]))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('channel/show')
            ->where('channel.id', $channel->id)
            ->where('channel.name', 'general')
            ->where('channel.workspace.id', $workspace->id)
        );
});

it('can create a channel', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->post(route('channel.store', $workspace), [
        'name' => 'general',
    ]);

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Channel created.'),
            ],
        ]);

    $channels = $workspace->channels;

    expect($channels->count())->toBe(1)
        ->and($channels->first()->name)->toBe('general')
        ->and($channels->first()->slug)->toBe('general');
});

it('infers the slug from the name and ignores a provided slug', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->post(route('channel.store', $workspace), [
        'name' => 'Product Updates',
        'slug' => 'custom-slug',
    ]);

    $response->assertSessionHasNoErrors();

    expect($workspace->channels()->first()->slug)->toBe('product-updates');
});

it('generates a unique slug per workspace when the name is taken', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);

    $response = $this->actingAs($user)->post(route('channel.store', $workspace), [
        'name' => 'general',
    ]);

    $response->assertSessionHasNoErrors();

    expect($workspace->channels()->where('slug', 'general-2')->exists())->toBeTrue();
});

it('validates the channel name', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->post(route('channel.store', $workspace), [
        'name' => 'ab',
    ]);

    $response->assertSessionHasErrors('name');

    expect($workspace->channels()->count())->toBe(0);
});

it('can update a channel name', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create(['name' => 'general']);

    $response = $this->actingAs($user)->patch(route('channel.update', [$workspace, $channel]), [
        'name' => 'random',
    ]);

    $response->assertRedirectBack();

    expect($channel->refresh()->name)->toBe('random')
        ->and($channel->slug)->toBe('random');
});

it('can delete a channel', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $response = $this->actingAs($user)->delete(route('channel.destroy', [$workspace, $channel]));

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Channel deleted.'),
            ],
        ]);

    expect($workspace->channels()->count())->toBe(0);
});

it('cannot manage channels of a workspace owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($otherUser, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->delete(route('channel.destroy', [$workspace, $channel]))
        ->assertNotFound();

    expect($workspace->channels()->count())->toBe(1);
});

it('cannot access a channel from a different workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $otherWorkspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($otherWorkspace)->create();

    $this->actingAs($user)->get(route('channel.show', [$workspace, $channel]))
        ->assertNotFound();
});
