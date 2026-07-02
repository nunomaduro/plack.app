import { Form } from '@inertiajs/react';
import { Check, X } from 'lucide-react';
import { accept, decline } from '@/routes/invitations';

type PendingInvitation = {
    code: string;
    workspace: {
        id: string;
        name: string;
    };
    invitedBy: string;
};

export default function PendingInvitations({
    invitations,
}: {
    invitations: PendingInvitation[];
}) {
    if (invitations.length === 0) {
        return null;
    }

    return (
        <div className="flex flex-col gap-[2px] text-[12.5px]">
            {invitations.map((invitation) => (
                <div
                    key={invitation.code}
                    className="flex items-center justify-between gap-2 px-2 py-[6px]"
                    data-test="pending-invitation"
                >
                    <div className="flex min-w-0 flex-col">
                        <span className="truncate text-dim">
                            {invitation.workspace.name}
                        </span>
                        <span className="truncate text-[10px] text-mute">
                            from {invitation.invitedBy}
                        </span>
                    </div>

                    <div className="flex flex-none items-center gap-1">
                        <Form {...accept.form(invitation.code)}>
                            {({ processing }) => (
                                <button
                                    type="submit"
                                    disabled={processing}
                                    aria-label="Accept invitation"
                                    data-test="accept-invitation"
                                    className="flex h-[22px] w-[22px] items-center justify-center border border-line text-green transition-colors hover:border-green disabled:opacity-50"
                                >
                                    <Check className="h-3 w-3" />
                                </button>
                            )}
                        </Form>

                        <Form {...decline.form(invitation.code)}>
                            {({ processing }) => (
                                <button
                                    type="submit"
                                    disabled={processing}
                                    aria-label="Decline invitation"
                                    data-test="decline-invitation"
                                    className="flex h-[22px] w-[22px] items-center justify-center border border-line text-mute transition-colors hover:border-destructive hover:text-destructive disabled:opacity-50"
                                >
                                    <X className="h-3 w-3" />
                                </button>
                            )}
                        </Form>
                    </div>
                </div>
            ))}
        </div>
    );
}

export type { PendingInvitation };
