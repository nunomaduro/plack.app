<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Support\Str;

final readonly class CreateChannel
{
    public function handle(Workspace $workspace, string $name): Channel
    {
        return $workspace->channels()->create([
            'name' => $name,
            'slug' => Str::slug($name),
        ]);
    }
}
