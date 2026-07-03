<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SearchMessages;
use App\Http\Requests\SearchMessagesRequest;
use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Http\JsonResponse;

final readonly class MessageSearchController
{
    public function __invoke(
        SearchMessagesRequest $request,
        Workspace $workspace,
        SearchMessages $searchMessages,
    ): JsonResponse {
        $channel = $request->validated('channel_id')
            ? $workspace->channels()->find($request->validated('channel_id'))
            : null;

        if ($request->validated('channel_id') && ! $channel instanceof Channel) {
            abort(404);
        }

        $results = $searchMessages->handle(
            $workspace,
            $request->validated('query'),
            $channel,
        );

        return response()->json($results);
    }
}
