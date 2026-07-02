<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class CreateWorkspace
{
    public function __construct(
        private CreateChannel $createChannel,
    ) {}

    public function handle(User $user, string $name): Workspace
    {
        return DB::transaction(function () use ($user, $name): Workspace {
            $slug = $this->generateUniqueSlug($name);

            $workspace = $user->workspaces()->create([
                'name' => $name,
                'slug' => $slug,
            ]);

            $this->createChannel->handle($workspace, 'general');

            return $workspace;
        });
    }

    private function generateUniqueSlug(string $name): string
    {
        $slug = Str::slug($name);
        $original = $slug;
        $counter = 2;

        while (Workspace::query()->where('slug', $slug)->exists()) {
            $slug = $original.'-'.$counter;
            $counter++;
        }

        return $slug;
    }
}
