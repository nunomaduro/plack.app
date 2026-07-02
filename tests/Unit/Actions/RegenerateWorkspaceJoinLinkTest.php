<?php

declare(strict_types=1);

use App\Actions\RegenerateWorkspaceJoinLink;
use App\Enums\WorkspaceType;
use App\Models\Workspace;
use Illuminate\Support\Str;

it('replaces the public workspace join code', function (): void {
    $workspace = Workspace::factory()->public()->create([
        'join_code' => 'old-public-join-code',
    ]);

    Str::createRandomStringsUsingSequence(['new-public-join-code']);

    resolve(RegenerateWorkspaceJoinLink::class)->handle($workspace);

    expect($workspace->refresh()->type)->toBe(WorkspaceType::Public)
        ->and($workspace->join_code)->toBe('new-public-join-code');
});
