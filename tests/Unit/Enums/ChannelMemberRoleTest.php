<?php

declare(strict_types=1);

use App\Enums\ChannelMemberRole;

it('may check if the role is an admin', function (): void {
    expect(ChannelMemberRole::Admin->isAdmin())->toBeTrue()
        ->and(ChannelMemberRole::Admin->isMember())->toBeFalse();
});

it('may check if the role is a member', function (): void {
    expect(ChannelMemberRole::Member->isMember())->toBeTrue()
        ->and(ChannelMemberRole::Member->isAdmin())->toBeFalse();
});
