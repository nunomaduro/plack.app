import { Head, Link } from '@inertiajs/react';
import CreateChannelDialog from '@/components/create-channel-dialog';
import EditChannelDialog from '@/components/edit-channel-dialog';
import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import { show as channelShow } from '@/routes/channel';
import { index, show } from '@/routes/workspace';
import type { BreadcrumbItem } from '@/types';

type Channel = {
    id: string;
    name: string;
    slug: string;
};

type Workspace = {
    id: string;
    name: string;
    slug: string;
    channels: Channel[];
};

export default function WorkspaceShow({ workspace }: { workspace: Workspace }) {
    const channels = workspace.channels;

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Workspaces',
            href: index(),
        },
        {
            title: workspace.name,
            href: show(workspace.slug),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={workspace.name} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading
                        title={workspace.name}
                        description="Connect channels to this workspace."
                    />
                </div>

                <div className="flex flex-col gap-4">
                    <div className="flex items-center justify-between">
                        <Heading variant="small" title="Channels" />

                        <CreateChannelDialog workspaceSlug={workspace.slug} />
                    </div>

                    {channels.length === 0 ? (
                        <div className="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 p-12 dark:border-sidebar-border">
                            <p className="text-sm text-muted-foreground">
                                No channels connected yet.
                            </p>

                            <CreateChannelDialog
                                workspaceSlug={workspace.slug}
                            />
                        </div>
                    ) : (
                        <ul className="flex flex-col gap-2">
                            {channels.map((channel) => (
                                <li
                                    key={channel.id}
                                    className="flex items-center justify-between rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                                >
                                    <Link
                                        href={channelShow({
                                            workspace: workspace.slug,
                                            channel: channel.slug,
                                        })}
                                        className="font-medium hover:underline"
                                    >
                                        {channel.name}
                                    </Link>

                                    <EditChannelDialog
                                        workspaceSlug={workspace.slug}
                                        channel={channel}
                                    />
                                </li>
                            ))}
                        </ul>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}
