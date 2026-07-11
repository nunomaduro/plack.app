<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Workspace;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class EnsureIsWorkspaceMember
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $workspace = $request->route('workspace');
        $user = $request->user();

        abort_unless($workspace instanceof Workspace && $user !== null, 404);

        $canEnter = $workspace->user_id === $user->id
            || $workspace->members()->whereKey($user->id)->exists();

        abort_unless($canEnter, 404);

        return $next($request);
    }
}
