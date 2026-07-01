<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;

final class CannotDeleteLastChannel extends Exception
{
    public function render(Request $request): RedirectResponse
    {
        Inertia::flash('toast', [
            'type' => 'error',
            'message' => __('A workspace must have at least one channel.'),
        ]);

        return back();
    }
}
