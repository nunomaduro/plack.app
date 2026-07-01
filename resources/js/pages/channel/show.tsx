import { Form, Head } from '@inertiajs/react';
import MessageController from '@/actions/App/Http/Controllers/MessageController';
import EditChannelDialog from '@/components/edit-channel-dialog';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import { show as channelShow } from '@/routes/channel';
import { index, show as workspaceShow } from '@/routes/workspace';
import type { BreadcrumbItem } from '@/types';

type Workspace = {
    id: string;
    name: string;
    slug: string;
};

type User = {
    id: string;
    name: string;
};

type Message = {
    id: string;
    body: string;
    created_at: string;
    user: User;
};

type Channel = {
    id: string;
    name: string;
    slug: string;
    workspace: Workspace;
    messages: Message[];
};

export default function ChannelShow({ channel }: { channel: Channel }) {
    const workspace = channel.workspace;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Workspaces',
            href: index(),
        },
        {
            title: workspace.name,
            href: workspaceShow(workspace.slug),
        },
        {
            title: channel.name,
            href: channelShow({
                workspace: workspace.slug,
                channel: channel.slug,
            }),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={channel.name} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-hidden rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading
                        title={channel.name}
                        description="Messages posted to this channel."
                    />

                    <EditChannelDialog
                        workspaceSlug={workspace.slug}
                        channel={channel}
                    />
                </div>

                {channel.messages.length === 0 ? (
                    <div className="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 p-12 dark:border-sidebar-border">
                        <p className="text-sm text-muted-foreground">
                            No messages yet.
                        </p>
                    </div>
                ) : (
                    <ul className="flex flex-1 flex-col gap-4 overflow-y-auto">
                        {channel.messages.map((message) => (
                            <li
                                key={message.id}
                                className="flex flex-col gap-1 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                            >
                                <div className="flex items-baseline gap-2">
                                    <span className="font-medium">
                                        {message.user.name}
                                    </span>

                                    <span className="text-xs text-muted-foreground">
                                        {new Date(
                                            message.created_at,
                                        ).toLocaleString()}
                                    </span>
                                </div>

                                <p className="text-sm whitespace-pre-wrap text-foreground">
                                    {message.body}
                                </p>
                            </li>
                        ))}
                    </ul>
                )}

                <Form
                    {...MessageController.store.form({
                        workspace: workspace.slug,
                        channel: channel.slug,
                    })}
                    options={{ preserveScroll: true }}
                    resetOnSuccess
                    className="flex flex-col gap-2"
                >
                    {({ processing, errors }) => (
                        <>
                            <div className="flex items-start gap-2">
                                <Input
                                    name="body"
                                    placeholder={`Message #${channel.name}`}
                                    autoComplete="off"
                                    autoFocus
                                    className="flex-1"
                                />

                                <Button type="submit" disabled={processing}>
                                    Send
                                </Button>
                            </div>

                            <InputError message={errors.body} />
                        </>
                    )}
                </Form>
            </div>
        </AppLayout>
    );
}
