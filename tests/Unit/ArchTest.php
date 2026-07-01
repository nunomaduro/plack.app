<?php

declare(strict_types=1);

use Illuminate\Contracts\Queue\ShouldQueue;

arch()->preset()->php();
arch()->preset()->strict();
arch()->preset()->laravel();
arch()->preset()->security()->ignoring([
    'assert',
]);

arch('controllers')
    ->expect('App\Http\Controllers')
    ->not->toBeUsed();

arch('notifications are queued')
    ->expect('App\Notifications')
    ->toImplement(ShouldQueue::class);

//
