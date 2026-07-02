<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateOrGetConversation;
use App\Http\Requests\StoreDirectMessageRequest;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final readonly class DirectMessageController
{
    public function index(#[CurrentUser] User $user): Response
    {
        $conversations = $user->conversations()
            ->with(['participants' => fn (Relation $query) => $query->whereKeyNot($user->id)])
            ->with('messages', fn (Relation $query) => $query->latest()->limit(1))
            ->latest()
            ->paginate(20);

        return Inertia::render('direct-message/index', [
            'conversations' => $conversations,
        ]);
    }

    public function store(
        StoreDirectMessageRequest $request,
        #[CurrentUser] User $user,
        CreateOrGetConversation $createOrGetConversation,
    ): RedirectResponse {
        $userId = $request->string('user_id')->value();

        if ($userId === $user->id) {
            return back()->withErrors([
                'user_id' => __('You cannot start a conversation with yourself.'),
            ]);
        }

        $otherUser = User::query()->findOrFail($userId);

        $conversation = $createOrGetConversation->handle($user, $otherUser);

        return to_route('direct-message.show', $conversation);
    }

    public function show(#[CurrentUser] User $user, Conversation $conversation): Response
    {
        abort_unless($conversation->participants()->whereKey($user->id)->exists(), 404);

        $messages = $conversation->messages()
            ->with('sender')
            ->latest()
            ->paginate(50);

        $otherParticipant = $conversation->participants()
            ->whereKeyNot($user->id)
            ->first();

        return Inertia::render('direct-message/show', [
            'conversation' => $conversation,
            'messages' => $messages,
            'otherParticipant' => $otherParticipant,
        ]);
    }
}
