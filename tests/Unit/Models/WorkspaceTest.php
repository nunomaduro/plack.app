<?php

declare(strict_types=1);

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
        ]);
});

it('has members and invitations', function (): void {
    $workspace = Workspace::factory()->create();
    $member = User::factory()->create();

    $workspace->members()->attach($member);
    WorkspaceInvitation::factory()->for($workspace)->create();

    expect($workspace->members->pluck('id')->all())->toBe([$member->id])
        ->and($workspace->invitations)->toHaveCount(1);
});
