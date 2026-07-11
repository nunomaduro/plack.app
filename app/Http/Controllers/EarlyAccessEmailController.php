<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateEarlyAccessEmail;
use App\Http\Requests\CreateEarlyAccessEmailRequest;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;

final readonly class EarlyAccessEmailController
{
    public function __invoke(
        CreateEarlyAccessEmailRequest $request,
        CreateEarlyAccessEmail $createEarlyAccessEmail,
    ): RedirectResponse {
        $createEarlyAccessEmail->handle($request->string('email')->value());

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __("You're on the list. We'll be in touch."),
        ]);

        return back();
    }
}
