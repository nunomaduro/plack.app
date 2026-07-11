<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Channel;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

final readonly class DeleteWorkspace
{
    public function __construct(private DeleteChannel $deleteChannel)
    {
        //
    }

    public function handle(Workspace $workspace): void
    {
        DB::transaction(function () use ($workspace): void {
            $workspace->members()->detach();
            $workspace->invitations()->delete();
            $workspace->channels()->each(fn (Channel $channel) => $this->deleteChannel->handle($channel));

            $workspace->delete();
        });
    }
}
