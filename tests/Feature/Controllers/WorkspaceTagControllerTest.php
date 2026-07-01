<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceTag;
use Inertia\Support\SessionKey;

it('can list workspace tags', function (): void {
    $user = User::factory()->create();

    WorkspaceTag::factory()
        ->count(3)
        ->for($user, 'owner')
        ->create();

    $this->actingAs($user)->getJson(route('workspace-tag.index'))
        ->assertStatus(200)
        ->assertJsonCount(3);
});

it('can create a workspace tag', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('workspace-tag.store'), [
        'name' => 'engineering',
    ]);

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Workspace tag created.'),
            ],
        ]);

    expect($user->workspaceTags()->count())->toBe(1)
        ->and($user->workspaceTags->first()->name)->toBe('engineering');
});

it('validates the workspace tag name', function (): void {
    $user = User::factory()->create();

    $this->actingAs($user)->post(route('workspace-tag.store'), [
        'name' => 'a',
    ])->assertSessionHasErrors('name');

    expect($user->workspaceTags()->count())->toBe(0);
});

it('cannot create a workspace tag with a name already used by the user', function (): void {
    $user = User::factory()->create();
    WorkspaceTag::factory()->for($user, 'owner')->create(['name' => 'engineering']);

    $this->actingAs($user)->post(route('workspace-tag.store'), [
        'name' => 'engineering',
    ])->assertSessionHasErrors('name');

    expect($user->workspaceTags()->count())->toBe(1);
});

it('allows different users to create workspace tags with the same name', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    WorkspaceTag::factory()->for($otherUser, 'owner')->create(['name' => 'engineering']);

    $this->actingAs($user)->post(route('workspace-tag.store'), [
        'name' => 'engineering',
    ])->assertSessionHasNoErrors();

    expect($user->workspaceTags()->count())->toBe(1);
});

it('can update a workspace tag name', function (): void {
    $user = User::factory()->create();
    $tag = WorkspaceTag::factory()->for($user, 'owner')->create(['name' => 'engineering']);

    $response = $this->actingAs($user)->patch(route('workspace-tag.update', $tag), [
        'name' => 'design',
    ]);

    $response->assertRedirectBack();

    expect($tag->refresh()->name)->toBe('design');
});

it('cannot update a workspace tag owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $tag = WorkspaceTag::factory()->for($otherUser, 'owner')->create(['name' => 'engineering']);

    $this->actingAs($user)->patch(route('workspace-tag.update', $tag), [
        'name' => 'design',
    ])->assertNotFound();

    expect($tag->refresh()->name)->toBe('engineering');
});

it('can delete a workspace tag', function (): void {
    $user = User::factory()->create();
    $tag = WorkspaceTag::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->delete(route('workspace-tag.destroy', $tag));

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Workspace tag deleted.'),
            ],
        ]);

    expect($user->workspaceTags()->count())->toBe(0);
});

it('cannot delete a workspace tag owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $tag = WorkspaceTag::factory()->for($otherUser, 'owner')->create();

    $this->actingAs($user)->delete(route('workspace-tag.destroy', $tag))
        ->assertNotFound();

    expect($otherUser->workspaceTags()->count())->toBe(1);
});

it('can attach a workspace tag to a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = WorkspaceTag::factory()->for($user, 'owner')->create();

    $response = $this->actingAs($user)->post(route('workspace-tag.attach', [$workspace, $tag]));

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Workspace tag attached.'),
            ],
        ]);

    expect($workspace->tags()->count())->toBe(1);
});

it('cannot attach a workspace tag to a workspace owned by another user', function (): void {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $workspace = Workspace::factory()->for($otherUser, 'owner')->create();
    $tag = WorkspaceTag::factory()->for($user, 'owner')->create();

    $this->actingAs($user)->post(route('workspace-tag.attach', [$workspace, $tag]))
        ->assertNotFound();

    expect($workspace->tags()->count())->toBe(0);
});

it('can detach a workspace tag from a workspace', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->for($user, 'owner')->create();
    $tag = WorkspaceTag::factory()->for($user, 'owner')->create();
    $workspace->tags()->attach($tag->id);

    $response = $this->actingAs($user)->delete(route('workspace-tag.detach', [$workspace, $tag]));

    $response->assertRedirectBack()
        ->assertSessionHas(SessionKey::FLASH_DATA, [
            'toast' => [
                'type' => 'success',
                'message' => __('Workspace tag detached.'),
            ],
        ]);

    expect($workspace->tags()->count())->toBe(0);
});
