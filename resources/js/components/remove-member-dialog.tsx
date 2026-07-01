import { Form } from '@inertiajs/react';
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
    workspaceId,
    member,
}: {
    workspaceId: string;
    member: Member;
}) {
    return (
        <Dialog>
            <DialogTrigger asChild>
                <Button variant="ghost" size="sm">
                    Remove
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
                        workspaceId,
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
