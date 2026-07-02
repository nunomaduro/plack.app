<?php

declare(strict_types=1);

namespace App\Queries;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class ListWorkspace
{
    /**
     * @return LengthAwarePaginator<int, Workspace>
     */
    public function get(User $user): LengthAwarePaginator
    {
        return $user
            ->workspaces()
            ->latest()
            ->paginate(10);
    }
}
