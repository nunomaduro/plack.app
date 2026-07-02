<?php

declare(strict_types=1);

use App\Enums\WorkspaceType;
use App\Models\User;
use App\Models\Workspace;
use App\Models\WorkspaceInvitation;

test('to array', function (): void {
    $workspace = Workspace::factory()->create()->fresh();

    expect(array_keys($workspace->toArray()))
        ->toBe([
            'id',
            'user_id',
            'name',
            'created_at',
            'updated_at',
            'slug',
            'type',
            'join_code',
        ]);
});

it('is private by default without a join code', function (): void {
    $workspace = Workspace::factory()->create();

    expect($workspace->type)->toBe(WorkspaceType::Private)
        ->and($workspace->join_code)->toBeNull();
});

it('may be public with a join code', function (): void {
    $workspace = Workspace::factory()->public()->create();

    expect($workspace->type)->toBe(WorkspaceType::Public)
        ->and($workspace->join_code)->toBeString()
        ->and($workspace->join_code)->toHaveLength(64);
});

it('has members and invitations', function (): void {
    $workspace = Workspace::factory()->create();
    $member = User::factory()->create();

    $workspace->members()->attach($member);
    WorkspaceInvitation::factory()->for($workspace)->create();

    expect($workspace->members->pluck('id')->all())->toBe([$member->id])
        ->and($workspace->invitations)->toHaveCount(1);
});
