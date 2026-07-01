<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\UserTag;
use App\Models\Workspace;
use Inertia\Support\SessionKey;

it('can list user tags', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    UserTag::factory()
        ->count(3)
        ->for($workspace)
        ->create();

    $this->actingAs($user)->getJson(route('user-tag.index', $workspace))
        ->assertStatus(200)
        ->assertJsonCount(3);
});

it('can create a user tag', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->post(route('user-tag.store', $workspace), [
        'name' => 'frontend',
    ]);

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('User tag created.'),
            ],
        ]);

    expect($workspace->userTags()->count())->toBe(1)
        ->and($workspace->userTags->first()->name)->toBe('frontend');
});

it('validates the user tag name', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();

    $this->actingAs($user)->post(route('user-tag.store', $workspace), [
        'name' => 'a',
    ])->assertSessionHasErrors('name');

    expect($workspace->userTags()->count())->toBe(0);
});

it('cannot create a user tag with a name already used in the workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    UserTag::factory()->for($workspace)->create(['name' => 'frontend']);

    $this->actingAs($user)->post(route('user-tag.store', $workspace), [
        'name' => 'frontend',
    ])->assertSessionHasErrors('name');

    expect($workspace->userTags()->count())->toBe(1);
});

it('allows different workspaces to have user tags with the same name', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $otherWorkspace = Workspace::factory()->for($user, 'owner')->create();
    UserTag::factory()->for($otherWorkspace)->create(['name' => 'frontend']);

    $this->actingAs($user)->post(route('user-tag.store', $workspace), [
        'name' => 'frontend',
    ])->assertSessionHasNoErrors();

    expect($workspace->userTags()->count())->toBe(1);
});

it('cannot create a user tag in a workspace owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($otherUser, 'owner')->create();

    $this->actingAs($user)->post(route('user-tag.store', $workspace), [
        'name' => 'frontend',
    ])->assertNotFound();

    expect($workspace->userTags()->count())->toBe(0);
});

it('can update a user tag name', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create(['name' => 'frontend']);

    $response = $this->actingAs($user)->patch(route('user-tag.update', [$workspace, $tag]), [
        'name' => 'backend',
    ]);

    $response->assertRedirectBack();

    expect($tag->refresh()->name)->toBe('backend');
});

it('cannot update a user tag in a workspace owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($otherUser, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create(['name' => 'frontend']);

    $this->actingAs($user)->patch(route('user-tag.update', [$workspace, $tag]), [
        'name' => 'backend',
    ])->assertNotFound();

    expect($tag->refresh()->name)->toBe('frontend');
});

it('can delete a user tag', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create();

    $response = $this->actingAs($user)->delete(route('user-tag.destroy', [$workspace, $tag]));

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('User tag deleted.'),
            ],
        ]);

    expect($workspace->userTags()->count())->toBe(0);
});

it('cannot delete a user tag in a workspace owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($otherUser, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create();

    $this->actingAs($user)->delete(route('user-tag.destroy', [$workspace, $tag]))
        ->assertNotFound();

    expect($otherUser->workspaces->first()->userTags()->count())->toBe(1);
});

it('can attach a user tag to a user', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create();
    $targetUser = User::factory()->create();

    $response = $this->actingAs($user)->post(route('user-tag.attach', [$workspace, $tag, $targetUser]));

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('User tag attached.'),
            ],
        ]);

    expect($tag->users()->count())->toBe(1);
});

it('cannot attach a user tag in a workspace owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($otherUser, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create();

    $this->actingAs($user)->post(route('user-tag.attach', [$workspace, $tag, $user]))
        ->assertNotFound();

    expect($tag->users()->count())->toBe(0);
});

it('can detach a user tag from a user', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = UserTag::factory()->for($workspace)->create();
    $targetUser = User::factory()->create();
    $tag->users()->attach($targetUser->id);

    $response = $this->actingAs($user)->delete(route('user-tag.detach', [$workspace, $tag, $targetUser]));

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('User tag detached.'),
            ],
        ]);

    expect($tag->users()->count())->toBe(0);
});
