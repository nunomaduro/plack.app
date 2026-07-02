import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';
import WorkspaceInvitationController from '@/actions/App/Http/Controllers/WorkspaceInvitationController';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';

type Invitation = {
    code: string;
    email: string;
};

export default function CancelInvitationDialog({
    workspaceSlug,
    invitation,
}: {
    workspaceSlug: string;
    invitation: Invitation;
}) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    aria-label="Cancel invitation"
                    data-test="cancel-invitation-trigger"
                >
                    <Trash2 />
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Cancel invitation?</DialogTitle>
                <DialogDescription>
                    The invitation sent to {invitation.email} will be revoked.
                    You can invite them again later.
                </DialogDescription>

                <Form
                    {...WorkspaceInvitationController.destroy.form([
                        workspaceSlug,
                        invitation.code,
                    ])}
                    options={{ preserveScroll: true }}
                    onSuccess={() => setOpen(false)}
                    className="space-y-6"
                >
                    {({ processing }) => (
                        <DialogFooter className="gap-2">
                            <DialogClose asChild>
                                <Button variant="secondary">
                                    Keep invitation
                                </Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                                data-test="cancel-invitation-submit"
                            >
                                Cancel invitation
                            </Button>
                        </DialogFooter>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
