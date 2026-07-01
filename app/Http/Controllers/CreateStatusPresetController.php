<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateStatusPreset;
use App\Http\Requests\CreateStatusPresetRequest;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class CreateStatusPresetController
{
    public function __invoke(
        CreateStatusPresetRequest $request,
        #[CurrentUser] User $user,
        CreateStatusPreset $createStatusPreset,
    ): RedirectResponse {
        $createStatusPreset->handle(
            $user,
            $request->string('emoji')->value(),
            $request->string('text')->value(),
        );

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Status preset created.'),
        ]);

        return back();
    }
}
