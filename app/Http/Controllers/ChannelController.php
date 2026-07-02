<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateChannel;
use App\Actions\DeleteChannel;
use App\Actions\UpdateChannel;
use App\Http\Requests\CreateChannelRequest;
use App\Http\Requests\DeleteChannelRequest;
use App\Http\Requests\UpdateChannelRequest;
use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class ChannelController
{
    public function show(Workspace $workspace, Channel $channel): Response
    {
        return Inertia::render('channel/show', [
            'channel' => $channel->load('workspace'),
        ]);
    }

    public function store(
        CreateChannelRequest $request,
        Workspace $workspace,
        CreateChannel $createChannel,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $createChannel->handle($workspace, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Channel created.'),
        ]);

        return back();
    }

    public function update(
        UpdateChannelRequest $request,
        Workspace $workspace,
        Channel $channel,
        UpdateChannel $updateChannel,
    ): RedirectResponse {
        $name = $request->string('name')->value();

        $updateChannel->handle($channel, $name);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Channel updated.'),
        ]);

        return back();
    }

    public function destroy(
        DeleteChannelRequest $request,
        Workspace $workspace,
        Channel $channel,
        DeleteChannel $deleteChannel,
    ): RedirectResponse {
        $deleteChannel->handle($channel);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Channel deleted.'),
        ]);

        return back();
    }
}
