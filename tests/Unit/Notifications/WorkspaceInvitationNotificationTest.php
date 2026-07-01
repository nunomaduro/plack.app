<?php

declare(strict_types=1);

use App\Models\WorkspaceInvitation;
use App\Notifications\WorkspaceInvitationNotification;
use Illuminate\Notifications\AnonymousNotifiable;

it('is delivered over mail', function (): void {
    $notification = new WorkspaceInvitationNotification(WorkspaceInvitation::factory()->create());

    expect($notification->via(new AnonymousNotifiable))->toBe(['mail']);
});

it('builds the invitation mail', function (): void {
    $invitation = WorkspaceInvitation::factory()->create();

    $mail = new WorkspaceInvitationNotification($invitation)->toMail(new AnonymousNotifiable);

    expect($mail->subject)->toContain($invitation->workspace->name)
        ->and($mail->actionUrl)->toBe(route('invitations.show', $invitation->code));
});
