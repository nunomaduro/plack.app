<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\EarlyAccessEmail;

final readonly class CreateEarlyAccessEmail
{
    public function handle(string $email): EarlyAccessEmail
    {
        return EarlyAccessEmail::query()->create([
            'email' => $email,
        ]);
    }
}
