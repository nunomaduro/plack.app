import { Head, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { index } from '@/routes/workspace';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Workspaces',
        href: index(),
    },
];

type Workspace = {
    id: string;
    name: string;
    created_at: string;
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
};

export default function WorkspaceList({
    workspaces,
}: {
    workspaces: Paginated<Workspace>;
}) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Workspaces" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <Heading title="Workspaces" description="Workspaces you own." />

                {workspaces.data.length === 0 ? (
                    <div className="flex flex-1 items-center justify-center rounded-xl border border-dashed border-sidebar-border/70 p-12 dark:border-sidebar-border">
                        <p className="text-sm text-muted-foreground">
                            No workspaces yet.
                        </p>
                    </div>
                ) : (
                    <ul className="flex flex-col gap-2">
                        {workspaces.data.map((workspace) => (
                            <li
                                key={workspace.id}
                                className="rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                            >
                                <span className="font-medium">
                                    {workspace.name}
                                </span>
                            </li>
                        ))}
                    </ul>
                )}

                {workspaces.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <Button
                            asChild
                            variant="outline"
                            size="sm"
                            disabled={!workspaces.prev_page_url}
                        >
                            {workspaces.prev_page_url ? (
                                <Link href={workspaces.prev_page_url}>
                                    Previous
                                </Link>
                            ) : (
                                <span>Previous</span>
                            )}
                        </Button>

                        <span className="text-sm text-muted-foreground">
                            Page {workspaces.current_page} of{' '}
                            {workspaces.last_page}
                        </span>

                        <Button
                            asChild
                            variant="outline"
                            size="sm"
                            disabled={!workspaces.next_page_url}
                        >
                            {workspaces.next_page_url ? (
                                <Link href={workspaces.next_page_url}>
                                    Next
                                </Link>
                            ) : (
                                <span>Next</span>
                            )}
                        </Button>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
