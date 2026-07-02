import { Form } from '@inertiajs/react';
import { UserPlus } from 'lucide-react';
import { useState } from 'react';
import WorkspaceInvitationController from '@/actions/App/Http/Controllers/WorkspaceInvitationController';
import InputError from '@/components/input-error';
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
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

export default function InviteMemberDialog({
    workspaceSlug,
}: {
    workspaceSlug: string;
}) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button data-test="invite-member-trigger">
                    <UserPlus />
                    Invite member
                </Button>
            </DialogTrigger>
            <DialogContent data-test="invite-member-dialog">
                <DialogTitle>Invite member</DialogTitle>
                <DialogDescription>
                    Enter the email address of the person you want to invite.
                </DialogDescription>

                <Form
                    {...WorkspaceInvitationController.store.form(workspaceSlug)}
                    options={{ preserveScroll: true }}
                    onSuccess={() => setOpen(false)}
                    resetOnSuccess
                    className="space-y-6"
                >
                    {({ processing, errors, resetAndClearErrors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="email">Email address</Label>

                                <Input
                                    id="email"
                                    name="email"
                                    type="email"
                                    placeholder="teammate@example.com"
                                    autoComplete="off"
                                    autoFocus
                                />

                                <InputError message={errors.email} />
                            </div>

                            <DialogFooter className="gap-2">
                                <DialogClose asChild>
                                    <Button
                                        variant="secondary"
                                        onClick={() => resetAndClearErrors()}
                                    >
                                        Cancel
                                    </Button>
                                </DialogClose>

                                <Button
                                    type="submit"
                                    disabled={processing}
                                    data-test="invite-member-submit"
                                >
                                    Send invitation
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
