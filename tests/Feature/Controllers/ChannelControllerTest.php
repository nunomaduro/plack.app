<?php

declare(strict_types=1);

use App\Enums\ChannelMemberRole;
use App\Enums\ChannelVisibility;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Inertia\Support\SessionKey;
use Inertia\Testing\AssertableInertia as Assert;

it('redirects to the first channel of the workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->get(route('workspace.show', $workspace))
        ->assertRedirectToRoute('channel.show', [$workspace, $channel]);
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
            ->where('workspace.id', $workspace->id)
            ->where('canManage', true)
            ->has('workspace.channels')
        );
});

it('lets a member view a channel without managing it', function (): void {
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();
    $workspace->members()->attach($member);

    $this->actingAs($member)->get(route('channel.show', [$workspace, $channel]))
        ->assertStatus(200)
        ->assertInertia(fn (Assert $page): Assert => $page
            ->component('channel/show')
            ->where('canManage', false)
        );
});

it('does not let a non-member view a channel', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->get(route('channel.show', [$workspace, $channel]))
        ->assertNotFound();
});

it('does not let a member manage channels', function (): void {
    $member = User::factory()->create();
    $workspace = Workspace::factory()->create();
    Channel::factory()->for($workspace)->create();
    $channel = Channel::factory()->for($workspace)->create();
    $workspace->members()->attach($member);

    $this->actingAs($member)->delete(route('channel.destroy', [$workspace, $channel]))
        ->assertNotFound();

    expect($workspace->channels()->count())->toBe(2);
});

it('can create a channel', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->post(route('channel.store', $workspace), [
        'name' => 'general',
        'visibility' => ChannelVisibility::Public->value,
    ]);

    $channels = $workspace->channels;

    $response->assertRedirectToRoute('channel.show', [$workspace, $channels->first()])
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Channel created.'),
            ],
        ]);

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
        'visibility' => ChannelVisibility::Public->value,
    ]);

    $response->assertSessionHasNoErrors();

    expect($workspace->channels()->first()->slug)->toBe('product-updates');
});

it('rejects a channel name already used in the same workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);

    $response = $this->actingAs($user)->post(route('channel.store', $workspace), [
        'name' => 'general',
    ]);

    $response->assertSessionHasErrors('name');

    expect($workspace->channels()->count())->toBe(1);
});

it('allows the same channel name in a different workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);

    $otherWorkspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->post(route('channel.store', $otherWorkspace), [
        'name' => 'general',
        'visibility' => ChannelVisibility::Public->value,
    ]);

    $response->assertSessionHasNoErrors();

    expect($otherWorkspace->channels()->where('name', 'general')->count())->toBe(1);
});

it('only channels admins are allowed to manage channels', function (): void {

    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->private()->create();

    $channel->members()->syncWithoutDetaching([
        $user->id => ['role' => ChannelMemberRole::Member->value],
    ]);

    $response = $this->actingAs($user)->patch(route('channel.update', [$workspace, $channel]), [
        'name' => $channel->name,
        'visibility' => ChannelVisibility::Public->value,
    ]);

    $response->assertNotFound();

});

it('can update a channel visibility', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->private()->create();

    $channel->members()->syncWithoutDetaching([
        $user->id => ['role' => ChannelMemberRole::Admin->value],
    ]);

    $response = $this->actingAs($user)->patch(route('channel.update', [$workspace, $channel]), [
        'name' => $channel->name,
        'visibility' => ChannelVisibility::Public->value,
    ]);

    $response->assertSessionHasNoErrors();

    expect($channel->fresh()->visibility)->toBe(ChannelVisibility::Public);

});

it('validates the channel visibility', function (): void {

    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->post(route('channel.store', $workspace), [
        'name' => 'general',
        'visibility' => 'some-invalid-visibility',
    ]);

    $response->assertSessionHasErrors('visibility');

    expect($workspace->channels()->count())->toBe(0);

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

    $channel->members()->syncWithoutDetaching([
        $user->id => ['role' => ChannelMemberRole::Admin->value],
    ]);

    $response = $this->actingAs($user)->patch(route('channel.update', [$workspace, $channel]), [
        'name' => 'random',
        'visibility' => ChannelVisibility::Public->value,
    ]);

    $channel->refresh();

    $response->assertRedirectToRoute('channel.show', [$workspace, $channel]);

    expect($channel->name)->toBe('random')
        ->and($channel->slug)->toBe('random');
});

it('rejects updating a channel to a name already used in the workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);
    $channel = Channel::factory()->for($workspace)->create(['name' => 'random', 'slug' => 'random']);

    $channel->members()->syncWithoutDetaching([
        $user->id => ['role' => ChannelMemberRole::Admin->value],
    ]);

    $response = $this->actingAs($user)->patch(route('channel.update', [$workspace, $channel]), [
        'name' => 'general',
    ]);

    $response->assertSessionHasErrors(['name', 'visibility']);

    expect($channel->refresh()->name)->toBe('random');
});

it('can delete a channel when others remain', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    Channel::factory()->for($workspace)->create(['name' => 'general', 'slug' => 'general']);
    $channel = Channel::factory()->for($workspace)->create(['name' => 'random', 'slug' => 'random']);

    $response = $this->actingAs($user)->delete(route('channel.destroy', [$workspace, $channel]));

    $response->assertRedirectToRoute('workspace.show', $workspace)
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Channel deleted.'),
            ],
        ]);

    expect($workspace->channels()->count())->toBe(1);
});

it('cannot delete the last channel of a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $response = $this->actingAs($user)->delete(route('channel.destroy', [$workspace, $channel]));

    $response->assertSessionHasErrors('channel');

    expect($workspace->channels()->count())->toBe(1);
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
