<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\Emoji;
use Illuminate\Http\JsonResponse;

final readonly class EmojiController
{
    /**
     * @return JsonResponse<array<int, array{name: string, value: string}>>
     */
    public function __invoke(): JsonResponse
    {
        $emojis = array_map(fn (Emoji $emoji): array => [
            'name' => $emoji->name,
            'value' => $emoji->value,
        ], Emoji::cases());

        return response()->json($emojis);
    }
}
