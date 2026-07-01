import { Form, Head, Link } from '@inertiajs/react';
import { Check, DoorOpen, Pencil, Trash2 } from 'lucide-react';
import CreateWorkspaceDialog from '@/components/create-workspace-dialog';
import DeleteWorkspaceDialog from '@/components/delete-workspace-dialog';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import {
    Tooltip,
    TooltipContent,
    TooltipProvider,
    TooltipTrigger,
} from '@/components/ui/tooltip';
import AppLayout from '@/layouts/app-layout';
import { accept, decline } from '@/routes/invitations';
import { channels, index, show } from '@/routes/workspace';
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
};

type PendingInvitation = {
    code: string;
    workspace: Workspace;
    invitedBy: string;
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
};

function EmptyState({ message }: { message: string }) {
    return (
        <div className="flex flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 p-12 dark:border-sidebar-border">
            <p className="text-sm text-muted-foreground">{message}</p>
        </div>
    );
}

function WorkspaceRow({ children }: { children: React.ReactNode }) {
    return (
        <li className="flex items-center justify-between gap-4 rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
            {children}
        </li>
    );
}

function EnterButton({ workspaceId }: { workspaceId: string }) {
    return (
        <Tooltip>
            <TooltipTrigger asChild>
                <Button
                    asChild
                    variant="ghost"
                    size="sm"
                    aria-label="Enter workspace"
                >
                    <Link href={channels(workspaceId)}>
                        <DoorOpen className="h-4 w-4" />
                    </Link>
                </Button>
            </TooltipTrigger>
            <TooltipContent>
                <p>Enter workspace</p>
            </TooltipContent>
        </Tooltip>
    );
}

export default function WorkspaceList({
    ownedWorkspaces,
    memberWorkspaces,
    pendingInvitations,
}: {
    ownedWorkspaces: Paginated<Workspace>;
    memberWorkspaces: Workspace[];
    pendingInvitations: PendingInvitation[];
}) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Workspaces" />

            <TooltipProvider>
                <div className="flex h-full flex-1 flex-col gap-8 overflow-x-auto rounded-xl p-4">
                    <section className="flex flex-col gap-4">
                        <div className="flex items-center justify-between">
                            <Heading
                                title="My Workspaces"
                                description="Workspaces you own."
                            />

                            <CreateWorkspaceDialog />
                        </div>

                        {ownedWorkspaces.data.length === 0 ? (
                            <EmptyState message="You don't own any workspaces yet." />
                        ) : (
                            <ul className="flex flex-col gap-2">
                                {ownedWorkspaces.data.map((workspace) => (
                                    <WorkspaceRow key={workspace.id}>
                                        <span className="font-medium">
                                            {workspace.name}
                                        </span>

                                        <div className="flex items-center gap-1">
                                            <Tooltip>
                                                <TooltipTrigger asChild>
                                                    <Button
                                                        asChild
                                                        variant="ghost"
                                                        size="sm"
                                                        aria-label="Workspace settings"
                                                    >
                                                        <Link
                                                            href={show(
                                                                workspace.id,
                                                            )}
                                                        >
                                                            <Pencil className="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                </TooltipTrigger>
                                                <TooltipContent>
                                                    <p>Workspace settings</p>
                                                </TooltipContent>
                                            </Tooltip>

                                            <EnterButton
                                                workspaceId={workspace.id}
                                            />

                                            <DeleteWorkspaceDialog
                                                workspace={workspace}
                                            />
                                        </div>
                                    </WorkspaceRow>
                                ))}
                            </ul>
                        )}

                        {ownedWorkspaces.last_page > 1 && (
                            <div className="flex items-center justify-between">
                                <Button
                                    asChild
                                    variant="outline"
                                    size="sm"
                                    disabled={!ownedWorkspaces.prev_page_url}
                                >
                                    {ownedWorkspaces.prev_page_url ? (
                                        <Link
                                            href={ownedWorkspaces.prev_page_url}
                                        >
                                            Previous
                                        </Link>
                                    ) : (
                                        <span>Previous</span>
                                    )}
                                </Button>

                                <span className="text-sm text-muted-foreground">
                                    Page {ownedWorkspaces.current_page} of{' '}
                                    {ownedWorkspaces.last_page}
                                </span>

                                <Button
                                    asChild
                                    variant="outline"
                                    size="sm"
                                    disabled={!ownedWorkspaces.next_page_url}
                                >
                                    {ownedWorkspaces.next_page_url ? (
                                        <Link
                                            href={ownedWorkspaces.next_page_url}
                                        >
                                            Next
                                        </Link>
                                    ) : (
                                        <span>Next</span>
                                    )}
                                </Button>
                            </div>
                        )}
                    </section>

                    <section className="flex flex-col gap-4">
                        <Heading
                            title="Invited Workspaces"
                            description="Workspaces you've been invited to."
                        />

                        {memberWorkspaces.length === 0 &&
                        pendingInvitations.length === 0 ? (
                            <EmptyState message="You have no workspace invitations." />
                        ) : (
                            <ul className="flex flex-col gap-2">
                                {memberWorkspaces.map((workspace) => (
                                    <WorkspaceRow key={workspace.id}>
                                        <span className="font-medium">
                                            {workspace.name}
                                        </span>

                                        <EnterButton
                                            workspaceId={workspace.id}
                                        />
                                    </WorkspaceRow>
                                ))}

                                {pendingInvitations.map((invitation) => (
                                    <WorkspaceRow key={invitation.code}>
                                        <div className="flex flex-col">
                                            <span className="font-medium">
                                                {invitation.workspace.name}
                                            </span>
                                            <span className="text-sm text-muted-foreground">
                                                Invited by{' '}
                                                {invitation.invitedBy}
                                            </span>
                                        </div>

                                        <div className="flex items-center gap-1">
                                            <Form
                                                {...accept.form(
                                                    invitation.code,
                                                )}
                                                options={{
                                                    preserveScroll: true,
                                                }}
                                            >
                                                {({ processing }) => (
                                                    <Tooltip>
                                                        <TooltipTrigger asChild>
                                                            <Button
                                                                type="submit"
                                                                variant="ghost"
                                                                size="sm"
                                                                disabled={
                                                                    processing
                                                                }
                                                                aria-label="Accept invitation"
                                                            >
                                                                <Check className="h-4 w-4" />
                                                            </Button>
                                                        </TooltipTrigger>
                                                        <TooltipContent>
                                                            <p>
                                                                Accept
                                                                invitation
                                                            </p>
                                                        </TooltipContent>
                                                    </Tooltip>
                                                )}
                                            </Form>

                                            <Form
                                                {...decline.form(
                                                    invitation.code,
                                                )}
                                                options={{
                                                    preserveScroll: true,
                                                }}
                                            >
                                                {({ processing }) => (
                                                    <Tooltip>
                                                        <TooltipTrigger asChild>
                                                            <Button
                                                                type="submit"
                                                                variant="ghost"
                                                                size="sm"
                                                                disabled={
                                                                    processing
                                                                }
                                                                aria-label="Decline invitation"
                                                            >
                                                                <Trash2 className="h-4 w-4" />
                                                            </Button>
                                                        </TooltipTrigger>
                                                        <TooltipContent>
                                                            <p>
                                                                Decline
                                                                invitation
                                                            </p>
                                                        </TooltipContent>
                                                    </Tooltip>
                                                )}
                                            </Form>
                                        </div>
                                    </WorkspaceRow>
                                ))}
                            </ul>
                        )}
                    </section>
                </div>
            </TooltipProvider>
        </AppLayout>
    );
}
