import { Head } from '@inertiajs/react';
import EditChannelDialog from '@/components/edit-channel-dialog';
import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import { show as channelShow } from '@/routes/channel';
import { index, show as workspaceShow } from '@/routes/workspace';
import type { BreadcrumbItem } from '@/types';

type Workspace = {
    id: string;
    name: string;
    slug: string;
};

type Channel = {
    id: string;
    name: string;
    workspace: Workspace;
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
            href: channelShow({ workspace: workspace.id, channel: channel.id }),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={channel.name} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading
                        title={channel.name}
                        description="Messages posted to this channel."
                    />

                    <EditChannelDialog
                        workspaceId={workspace.id}
                        channel={channel}
                    />
                </div>

                <div className="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 p-12 dark:border-sidebar-border">
                    <p className="text-sm text-muted-foreground">
                        No messages yet.
                    </p>
                </div>
            </div>
        </AppLayout>
    );
}
