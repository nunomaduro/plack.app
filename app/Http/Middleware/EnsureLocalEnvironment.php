<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureLocalEnvironment
{
    /**
     * The application is not publicly available yet, so every route guarded by
     * this middleware is only reachable locally (the test suite is allowed so
     * it can still exercise these routes) and returns a 404 everywhere else.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_unless(app()->environment('local', 'testing'), 404);

        return $next($request);
    }
}
