<?php

declare(strict_types=1);

use App\Actions\UpdateChannelVisibility;
use App\Enums\ChannelVisibility;
use App\Models\Channel;

it('may change the channel visibility', function (): void {

    $privateChannel = Channel::factory()
        ->private()
        ->create([
            'name' => 'barrentix',
        ]);

    resolve(UpdateChannelVisibility::class)
        ->handle($privateChannel, ChannelVisibility::Public);

    expect($privateChannel->refresh()->visibility)
        ->toBe(ChannelVisibility::Public);

});
