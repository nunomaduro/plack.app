<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\DeleteStatusPreset;
use App\Http\Requests\DeleteStatusPresetRequest;
use App\Models\StatusPreset;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class DeleteStatusPresetController
{
    public function __invoke(
        DeleteStatusPresetRequest $request,
        StatusPreset $statusPreset,
        DeleteStatusPreset $deleteStatusPreset,
    ): RedirectResponse {
        $deleteStatusPreset->handle($statusPreset);

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Status preset deleted.'),
        ]);

        return back();
    }
}
