<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeletePin;
use App\Http\Requests\DeletePinRequest;
use App\Models\Channel;
use App\Models\Pin;
use App\Models\Workspace;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class DeletePinController
{
    public function __invoke(
        DeletePinRequest $request,
        Workspace $workspace,
        Channel $channel,
        Pin $pin,
        DeletePin $deletePin,
    ): RedirectResponse {
        $deletePin->handle($pin);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Pin removed.'),
        ]);

        return back();
    }
}
