import { Head } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { index, show } from '@/routes/workspace';
import type { BreadcrumbItem } from '@/types';

type Workspace = {
    id: string;
    name: string;
};

export default function WorkspaceShow({ workspace }: { workspace: Workspace }) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Workspaces',
            href: index(),
        },
        {
            title: workspace.name,
            href: show(workspace.id),
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
                    <Heading variant="small" title="Channels" />

                    <div className="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 p-12 dark:border-sidebar-border">
                        <p className="text-sm text-muted-foreground">
                            No channels connected yet.
                        </p>

                        <Button variant="outline" disabled>
                            Connect Slack
                        </Button>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}
