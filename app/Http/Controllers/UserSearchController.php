<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class UserSearchController
{
    public function __invoke(Request $request, #[CurrentUser] User $user): JsonResponse
    {
        $query = $request->string('q');

        if ($query->isEmpty()) {
            return response()->json([]);
        }

        $users = User::query()
            ->whereKeyNot($user->id)
            ->where(function (Builder $queryBuilder) use ($query): void {
                $queryBuilder
                    ->where('name', 'like', '%'.$query.'%')
                    ->orWhere('email', 'like', '%'.$query.'%');
            })
            ->limit(10)
            ->get(['id', 'name']);

        return response()->json($users);
    }
}
