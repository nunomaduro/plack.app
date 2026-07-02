import { Form } from '@inertiajs/react';
import { Trash2 } from 'lucide-react';
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

type Channel = {
    id: string;
    name: string;
    slug: string;
};

export default function DeleteChannelDialog({
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
                <Button variant="ghost" size="icon" aria-label="Delete channel">
                    <Trash2 />
                </Button>
            </DialogTrigger>
            <DialogContent>
                <DialogTitle>Delete channel</DialogTitle>
                <DialogDescription>
                    Are you sure you want to delete{' '}
                    <span className="font-medium">#{channel.name}</span>? This
                    cannot be undone.
                </DialogDescription>

                <Form
                    {...ChannelController.destroy.form({
                        workspace: workspaceSlug,
                        channel: channel.slug,
                    })}
                    options={{
                        preserveScroll: true,
                    }}
                    onSuccess={() => setOpen(false)}
                    className="space-y-6"
                >
                    {({ processing, errors }) => (
                        <>
                            <InputError message={errors.channel} />

                            <DialogFooter className="gap-2">
                                <DialogClose asChild>
                                    <Button variant="secondary">Cancel</Button>
                                </DialogClose>

                                <Button
                                    type="submit"
                                    variant="destructive"
                                    disabled={processing}
                                >
                                    Delete
                                </Button>
                            </DialogFooter>
                        </>
                    )}
                </Form>
            </DialogContent>
        </Dialog>
    );
}
