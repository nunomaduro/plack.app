<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Http\JsonResponse;

final readonly class NotificationController
{
    public function index(#[CurrentUser] User $user): JsonResponse
    {
        return response()->json(
            $user->notifications()->take(20)->get()
        );
    }

    public function update(#[CurrentUser] User $user, string $id): JsonResponse
    {
        $user->notifications()->findOrFail($id)->markAsRead();

        return response()->json(['success' => true]);
    }

    public function store(#[CurrentUser] User $user): JsonResponse
    {
        $user->unreadNotifications->markAsRead();

        return response()->json(['success' => true]);
    }
}
