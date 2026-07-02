<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Collection;

final readonly class CreateOrGetConversation
{
    public function handle(User $userA, User $userB): Conversation
    {
        $existing = $this->findExisting($userA, $userB);

        if ($existing instanceof Conversation) {
            return $existing;
        }

        $conversation = Conversation::query()->create();

        $participants = $userA->is($userB) ? [$userA->id] : [$userA->id, $userB->id];

        $conversation->participants()->attach($participants);

        return $conversation;
    }

    private function findExisting(User $userA, User $userB): ?Conversation
    {
        $idsA = $this->participantIds($userA);
        $idsB = $this->participantIds($userB);

        $common = $idsA->intersect($idsB);

        if ($common->isEmpty()) {
            return null;
        }

        /** @var Conversation|null $conversation */
        $conversation = Conversation::query()->find($common->first());

        return $conversation;
    }

    /**
     * @return Collection<int, string>
     */
    private function participantIds(User $user): Collection
    {
        /** @var Collection<int, string> $ids */
        $ids = $user->conversations()->pluck('conversation_id');

        return $ids;
    }
}
