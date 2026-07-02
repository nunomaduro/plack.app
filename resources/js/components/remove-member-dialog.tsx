import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import WorkspaceMemberController from '@/actions/App/Http/Controllers/WorkspaceMemberController';
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

type Member = {
    id: string;
    name: string;
};

export default function RemoveMemberDialog({
    workspaceSlug,
    member,
}: {
    workspaceSlug: string;
    member: Member;
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
                                aria-label="Remove member"
                            >
                                <Trash2 className="h-4 w-4" />
                            </Button>
                        </DialogTrigger>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>Remove member</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
            <DialogContent>
                <DialogTitle>Remove {member.name}?</DialogTitle>
                <DialogDescription>
                    They will lose access to this workspace. You can invite them
                    again later.
                </DialogDescription>

                <Form
                    {...WorkspaceMemberController.destroy.form([
                        workspaceSlug,
                        member.id,
                    ])}
                    options={{ preserveScroll: true }}
                    className="space-y-6"
                >
                    {({ processing }) => (
                        <DialogFooter className="gap-2">
                            <DialogClose asChild>
                                <Button variant="secondary">Cancel</Button>
                            </DialogClose>

                            <Button
                                type="submit"
                                variant="destructive"
                                disabled={processing}
                            >
                                Remove member
                            </Button>
                        </DialogFooter>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
