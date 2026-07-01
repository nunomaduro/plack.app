<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Workspace;
use Illuminate\Http\Request;
use Inertia\Middleware;

final class HandleInertiaRequests extends Middleware
{
    /**
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $currentWorkspace = $request->route('workspace');

        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $user,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
            'navWorkspaces' => fn () => $user
                ? $user->workspaces()->orderBy('name')->get(['id', 'name', 'slug'])
                : [],
            'currentWorkspace' => fn () => $currentWorkspace instanceof Workspace
                ? [
                    'id' => $currentWorkspace->id,
                    'name' => $currentWorkspace->name,
                    'slug' => $currentWorkspace->slug,
                    'channels' => $currentWorkspace->channels()->oldest()->get(['id', 'name', 'slug']),
                ]
                : null,
        ];
    }
}
