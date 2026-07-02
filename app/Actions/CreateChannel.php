<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\ChannelMemberRole;
use App\Enums\ChannelVisibility;
use App\Events\ChannelCreated;
use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

final readonly class CreateChannel
{
    public function __construct(private AddChannelMember $addChannelMember) {}

    public function handle(Workspace $workspace, string $name, ChannelVisibility $channelVisibility = ChannelVisibility::Public): Channel
    {
        $channel = DB::transaction(function () use ($workspace, $name, $channelVisibility): Channel {
            $channel = $workspace->channels()->create([
                'name' => $name,
                'visibility' => $channelVisibility,
            ]);

            $this->addChannelMember->handle($channel, $workspace->owner, ChannelMemberRole::Admin);

            return $channel;
        });

        broadcast(new ChannelCreated($channel))->toOthers();

        return $channel;
    }
}
