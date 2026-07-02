<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreatePin;
use App\Http\Requests\CreatePinRequest;
use App\Models\Channel;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class CreatePinController
{
    public function __invoke(
        CreatePinRequest $request,
        Workspace $workspace,
        Channel $channel,
        #[CurrentUser] User $user,
        CreatePin $createPin,
    ): RedirectResponse {
        $message = $channel->messages()->findOrFail($request->string('message_id')->value());

        $createPin->handle($user, $channel, $message);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Pin added.'),
        ]);

        return back();
    }
}
