import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
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
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';

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
    return (
        <Dialog>
            <TooltipProvider>
                <Tooltip>
                    <TooltipTrigger asChild>
                        <DialogTrigger asChild>
                            <Button
                                variant="ghost"
                                size="sm"
                                aria-label="Delete workspace"
                            >
                                <Trash2 className="h-4 w-4" />
                            </Button>
                        </DialogTrigger>
                    </TooltipTrigger>
                    <TooltipContent>
                        <p>Delete workspace</p>
                    </TooltipContent>
                </Tooltip>
            </TooltipProvider>
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
                                Delete workspace
                            </Button>
                        </DialogFooter>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
