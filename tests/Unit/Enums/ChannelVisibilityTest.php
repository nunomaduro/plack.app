<?php

declare(strict_types=1);

use App\Enums\ChannelVisibility;

it('may check if channel is public', function (): void {

    $publicChannel = ChannelVisibility::Public;

    expect($publicChannel->isPublic())->toBeTrue()
        ->and($publicChannel->isPrivate())->toBeFalse();

});

it('may check if channel is private', function (): void {

    $privateChannel = ChannelVisibility::Private;

    expect($privateChannel->isPrivate())->toBeTrue()
        ->and($privateChannel->isPublic())->toBeFalse();

});

it('may list its options', function (): void {
    expect(ChannelVisibility::options())->toBe([
        ['public' => 'Public'],
        ['private' => 'Private'],
    ]);
});
