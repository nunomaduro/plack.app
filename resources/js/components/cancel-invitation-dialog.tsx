import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
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
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

type Invitation = {
    code: string;
    email: string;
};

export default function CancelInvitationDialog({
    workspaceId,
    invitation,
}: {
    workspaceId: string;
    invitation: Invitation;
}) {
    return (
        <Dialog>
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger asChild>
                        <DialogTrigger asChild>
                            <Button
                                variant="ghost"
                                size="sm"
                                aria-label="Cancel invitation"
                            >
                                <Trash2 className="h-4 w-4" />
                            </Button>
                        </DialogTrigger>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>Cancel invitation</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
            <DialogContent>
                <DialogTitle>Cancel invitation?</DialogTitle>
                <DialogDescription>
                    The invitation sent to {invitation.email} will be revoked.
                    You can invite them again later.
                </DialogDescription>

                <Form
                    {...WorkspaceInvitationController.destroy.form([
                        workspaceId,
                        invitation.code,
                    ])}
                    options={{ preserveScroll: true }}
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
