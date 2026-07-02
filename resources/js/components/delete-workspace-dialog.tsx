import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
import { useState } from 'react';
import WorkspaceController from '@/actions/App/Http/Controllers/WorkspaceController';
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

type Workspace = {
    id: string;
    name: string;
    slug: string;
};

export default function DeleteWorkspaceDialog({
    workspace,
}: {
    workspace: Workspace;
}) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button
                    variant="destructive"
                    size="sm"
                    aria-label="Delete workspace"
                    data-test="delete-workspace-trigger"
                >
                    <Trash2 />
                    Delete workspace
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Delete “{workspace.name}”?</DialogTitle>
                <DialogDescription>
                    Once deleted, this workspace and all of its members and
                    invitations will be permanently removed. This cannot be
                    undone.
                </DialogDescription>

                <Form
                    {...WorkspaceController.destroy.form(workspace.slug)}
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
                                data-test="delete-workspace-submit"
                            >
                                Delete workspace
                            </Button>
                        </DialogFooter>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
