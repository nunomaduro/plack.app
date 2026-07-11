<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\WorkspaceInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

final class WorkspaceInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly WorkspaceInvitation $invitation) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $workspace = $this->invitation->workspace;

        return (new MailMessage)
            ->subject(__('You have been invited to join :workspace', ['workspace' => $workspace->name]))
            ->line(__(':inviter has invited you to join the :workspace workspace.', [
                'inviter' => $this->invitation->inviter->name,
                'workspace' => $workspace->name,
            ]))
            ->action(__('Log in'), route('login', ['invitation' => $this->invitation->code]));
    }
}
