<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Message;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

final readonly class DeleteUser
{
    public function __construct(
        private DeleteWorkspace $deleteWorkspace,
        private DeleteMessage $deleteMessage,
    ) {
        //
    }

    public function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            /**  @todo Make sure we dont delete messages from the groups i dont own */
            $user->messages()->each(fn (Message $message) => $this->deleteMessage->handle($message));

            $user->ownedWorkspaces()->each(fn (Workspace $workspace) => $this->deleteWorkspace->handle($workspace));

            $user->delete();
        });
    }
}
