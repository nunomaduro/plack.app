<?php

declare(strict_types=1);

namespace App\Queries;

use App\Enums\WorkspaceType;
use App\Models\Workspace;

final readonly class FindPendingWorkspaceJoin
{
    /**
     * @return array{code: string, workspace: array{id: string, name: string}}|null
     */
    public function get(mixed $code): ?array
    {
        if (! is_string($code) || $code === '') {
            return null;
        }

        $workspace = $this->workspace($code);

        if (! $workspace instanceof Workspace) {
            return null;
        }

        return [
            'code' => $code,
            'workspace' => [
                'id' => $workspace->id,
                'name' => $workspace->name,
            ],
        ];
    }

    public function workspace(string $code): ?Workspace
    {
        if ($code === '') {
            return null;
        }

        return Workspace::query()
            ->where('type', WorkspaceType::Public->value)
            ->where('join_code', $code)
            ->first();
    }
}
