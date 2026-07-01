<?php

declare(strict_types=1);

use App\Enums\Emoji;
use App\Models\Channel;
use App\Models\Reaction;
use App\Models\User;
use App\Models\Workspace;
use Inertia\Support\SessionKey;

it('can create a reaction', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $response = $this->actingAs($user)->post(route('reaction.store', [$workspace, $channel]), [
        'emoji' => '👍',
    ]);

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Reaction added.'),
            ],
        ]);

    expect($channel->reactions()->count())->toBe(1)
        ->and($channel->reactions()->first()->emoji)->toBe(Emoji::ThumbsUp);
});

it('validates the emoji field is required', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('reaction.store', [$workspace, $channel]), [
        'emoji' => '',
    ])->assertSessionHasErrors('emoji');

    expect($channel->reactions()->count())->toBe(0);
});

it('validates the emoji max length', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('reaction.store', [$workspace, $channel]), [
        'emoji' => str_repeat('a', 17),
    ])->assertSessionHasErrors('emoji');

    expect($channel->reactions()->count())->toBe(0);
});

it('can delete a reaction', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();
    $reaction = Reaction::factory()->for($user)->for($channel, 'reactable')->create();

    $response = $this->actingAs($user)->delete(route('reaction.destroy', [$workspace, $channel, $reaction]));

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Reaction removed.'),
            ],
        ]);

    expect($channel->reactions()->count())->toBe(0);
});

it('cannot delete a reaction owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();
    $reaction = Reaction::factory()->for($otherUser)->for($channel, 'reactable')->create();

    $this->actingAs($user)->delete(route('reaction.destroy', [$workspace, $channel, $reaction]))
        ->assertNotFound();

    expect(Reaction::query()->whereKey($reaction->id)->exists())->toBeTrue();
});

it('requires authentication to create a reaction', function (): void {
    $workspace = Workspace::factory()->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->post(route('reaction.store', [$workspace, $channel]), [
        'emoji' => '👍',
    ])->assertRedirect(route('login'));
});

it('requires workspace access to create a reaction', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($otherUser, 'owner')->create();
    $channel = Channel::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('reaction.store', [$workspace, $channel]), [
        'emoji' => '👍',
    ])->assertNotFound();

    expect($channel->reactions()->count())->toBe(0);
});
