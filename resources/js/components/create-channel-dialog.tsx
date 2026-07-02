import { Form } from '@inertiajs/react';
import { Plus } from 'lucide-react';
import { useState } from 'react';
import ChannelController from '@/actions/App/Http/Controllers/ChannelController';
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

export default function CreateChannelDialog({
    workspaceSlug,
}: {
    workspaceSlug: string;
}) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button data-test="create-channel-trigger">
                    <Plus />
                    <span className="hidden sm:inline">New channel</span>
                </Button>
            </DialogTrigger>
            <DialogContent data-test="create-channel-dialog">
                <DialogTitle>Create channel</DialogTitle>
                <DialogDescription>
                    Enter a name for your new channel.
                </DialogDescription>

                <Form
                    {...ChannelController.store.form(workspaceSlug)}
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
                                <Label htmlFor="name">Channel name</Label>

                                <Input
                                    id="name"
                                    name="name"
                                    placeholder="general"
                                    autoComplete="off"
                                    autoFocus
                                />

                                <InputError message={errors.name} />
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
                                    data-test="create-channel-submit"
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
