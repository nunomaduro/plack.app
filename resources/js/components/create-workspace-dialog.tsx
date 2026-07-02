import { Form } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { type ReactNode, useState } from 'react';
import WorkspaceController from '@/actions/App/Http/Controllers/WorkspaceController';
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

export default function CreateWorkspaceDialog({
    open: controlledOpen,
    onOpenChange,
    trigger,
}: {
    open?: boolean;
    onOpenChange?: (open: boolean) => void;
    trigger?: ReactNode | null;
} = {}) {
    const [uncontrolledOpen, setUncontrolledOpen] = useState(false);
    const isControlled = controlledOpen !== undefined;
    const open = isControlled ? controlledOpen : uncontrolledOpen;

    const setOpen = (value: boolean) => {
        onOpenChange?.(value);

        if (!isControlled) {
            setUncontrolledOpen(value);
        }
    };

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            {trigger !== null && (
                <DialogTrigger asChild>
                    {trigger ?? (
                        <Button data-test="create-workspace-trigger">
                            <Plus />
                            New workspace
                        </Button>
                    )}
                </DialogTrigger>
            )}
            <DialogContent data-test="create-workspace-dialog">
                <DialogTitle>Create workspace</DialogTitle>
                <DialogDescription>
                    Enter a name for your new workspace.
                </DialogDescription>

                <Form
                    {...WorkspaceController.store.form()}
                    options={{
                        preserveScroll: true,
                    }}
                    onSuccess={() => setOpen(false)}
                    resetOnSuccess
                    className="space-y-6"
                >
                    {({ processing, errors, resetAndClearErrors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Workspace name</Label>

                                <Input
                                    id="name"
                                    name="name"
                                    placeholder="My workspace"
                                    autoComplete="off"
                                    autoFocus
                                />

                                <InputError message={errors.name} />
                            </div>

                            <div className="grid gap-2">
                                <Label>Workspace type</Label>

                                <div className="grid grid-cols-2 gap-2">
                                    <label
                                        data-test="create-workspace-private"
                                        className="cursor-pointer border border-line bg-ink-950 px-3 py-3 text-[12px] text-dim transition-colors has-checked:border-amber has-checked:text-amber"
                                    >
                                        <input
                                            type="radio"
                                            name="type"
                                            value="private"
                                            defaultChecked
                                            className="sr-only"
                                        />
                                        <span className="block font-medium">
                                            Private
                                        </span>
                                        <span className="mt-1 block text-[10px] text-mute">
                                            Invite-only access.
                                        </span>
                                    </label>

                                    <label
                                        data-test="create-workspace-public"
                                        className="cursor-pointer border border-line bg-ink-950 px-3 py-3 text-[12px] text-dim transition-colors has-checked:border-amber has-checked:text-amber"
                                    >
                                        <input
                                            type="radio"
                                            name="type"
                                            value="public"
                                            className="sr-only"
                                        />
                                        <span className="block font-medium">
                                            Public
                                        </span>
                                        <span className="mt-1 block text-[10px] text-mute">
                                            Join by shared link.
                                        </span>
                                    </label>
                                </div>

                                <InputError message={errors.type} />
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
                                    data-test="create-workspace-submit"
                                >
                                    Create
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
