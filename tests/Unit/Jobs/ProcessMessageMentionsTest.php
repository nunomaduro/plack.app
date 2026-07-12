<?php

declare(strict_types=1);

use App\Jobs\ProcessMessageMentions;
use App\Models\Channel;
use App\Models\User;

it('attaches mentioned users to the message', function (): void {
    $channel = Channel::factory()->create();
    $sender = User::factory()->create();
    $mentioned = User::factory()->create(['name' => 'Alice Smith']);

    $message = $channel->messages()->create([
        'user_id' => $sender->id,
        'body' => 'Hey @Alice.Smith, check this out!',
    ]);

    (new ProcessMessageMentions($message))->handle(resolve(App\Actions\ParseMentions::class));

    expect($message->mentions()->get())->toHaveCount(1)
        ->and($message->mentions()->first()->id)->toBe($mentioned->id);
});

it('does not attach mentions when none exist', function (): void {
    $channel = Channel::factory()->create();
    $sender = User::factory()->create();

    $message = $channel->messages()->create([
        'user_id' => $sender->id,
        'body' => 'No mentions here.',
    ]);

    (new ProcessMessageMentions($message))->handle(resolve(App\Actions\ParseMentions::class));

    expect($message->mentions()->get())->toBeEmpty();
});
