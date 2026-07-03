import { Form, usePage } from '@inertiajs/react';
import { Pencil } from 'lucide-react';
import { type ReactNode, useState } from 'react';
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
import {
    Select,
    SelectContent,
    SelectGroup,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';

type Channel = {
    id: string;
    name: string;
    slug: string;
    visibility: string;
};

export default function EditChannelDialog({
    workspaceSlug,
    channel,
    trigger,
}: {
    workspaceSlug: string;
    channel: Channel;
    trigger?: ReactNode;
}) {
    const [open, setOpen] = useState(false);
    const { channelVisibilityOptions } = usePage().props;

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                {trigger ?? (
                    <Button
                        variant="ghost"
                        size="icon"
                        aria-label="Edit channel"
                    >
                        <Pencil />
                    </Button>
                )}
            </DialogTrigger>
            <DialogContent data-test="edit-channel-dialog">
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

                            <div className="grid gap-2">
                                <Label htmlFor="visibility">Visibility</Label>

                                <Select
                                    name="visibility"
                                    defaultValue={channel.visibility}
                                >
                                    <SelectTrigger className="w-full">
                                        <SelectValue />
                                    </SelectTrigger>
                                    <SelectContent>
                                        <SelectGroup>
                                            {channelVisibilityOptions.map(
                                                (option) =>
                                                    Object.entries(option).map(
                                                        ([value, label]) => (
                                                            <SelectItem
                                                                key={value}
                                                                value={value}
                                                            >
                                                                {label}
                                                            </SelectItem>
                                                        ),
                                                    ),
                                            )}
                                        </SelectGroup>
                                    </SelectContent>
                                </Select>

                                <InputError message={errors.visibility} />
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
                                    data-test="edit-channel-submit"
                                >
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
