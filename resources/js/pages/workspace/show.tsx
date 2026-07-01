import { Head } from '@inertiajs/react';
import CreateChannelDialog from '@/components/create-channel-dialog';
import Heading from '@/components/heading';
import AppLayout from '@/layouts/app-layout';
import { index, show } from '@/routes/workspace';
import type { BreadcrumbItem, WorkspaceSummary } from '@/types';

export default function WorkspaceShow({
    workspace,
}: {
    workspace: WorkspaceSummary;
}) {
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
                <Heading
                    title={workspace.name}
                    description="Create a channel to start the conversation."
                />

                <div className="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 p-12 dark:border-sidebar-border">
                    <p className="text-sm text-muted-foreground">
                        No channels yet.
                    </p>

                    <CreateChannelDialog workspace={workspace.slug} />
                </div>
            </div>
        </AppLayout>
    );
}
