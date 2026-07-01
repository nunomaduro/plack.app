<?php

declare(strict_types=1);

use App\Models\User;
use App\Models\Workspace;

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'two_factor_confirmed_at',
            'created_at',
            'updated_at',
        ]);
});

it('belongs to member workspaces', function (): void {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();

    $workspace->members()->attach($user);

    expect($user->memberWorkspaces->pluck('id')->all())->toBe([$workspace->id]);
});
