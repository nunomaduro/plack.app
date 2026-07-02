<?php

declare(strict_types=1);

use App\Actions\DeletePin;
use App\Models\Pin;

it('may delete a pin', function (): void {
    $pin = Pin::factory()->create();

    resolve(DeletePin::class)->handle($pin);

    expect($pin->fresh()->trashed())->toBeTrue();
});
