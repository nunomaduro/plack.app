import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';
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
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    aria-label="Remove member"
                    data-test="remove-member-trigger"
                >
                    <Trash2 />
                </Button>
            </DialogTrigger>
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
                    onSuccess={() => setOpen(false)}
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
                                data-test="remove-member-submit"
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
