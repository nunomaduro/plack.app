import { Form } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
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

type Channel = {
    id: string;
    name: string;
    slug: string;
};

export default function EditChannelDialog({
    workspaceSlug,
    channel,
}: {
    workspaceSlug: string;
    channel: Channel;
}) {
    const [open, setOpen] = useState(false);

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button variant="ghost" size="icon" aria-label="Edit channel">
                    <Pencil />
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Edit channel</DialogTitle>
                <DialogDescription>
                    Update the name of your channel.
                </DialogDescription>

                <Form
                    {...ChannelController.update.form({
                        workspace: workspaceSlug,
                        channel: channel.slug,
                    })}
                    options={{
                        preserveScroll: true,
                    }}
                    onSuccess={() => setOpen(false)}
                    className="space-y-6"
                >
                    {({ processing, errors, resetAndClearErrors }) => (
                        <>
                            <div className="grid gap-2">
                                <Label htmlFor="name">Channel name</Label>

                                <Input
                                    id="name"
                                    name="name"
                                    defaultValue={channel.name}
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

                                <Button type="submit" disabled={processing}>
                                    Save
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
