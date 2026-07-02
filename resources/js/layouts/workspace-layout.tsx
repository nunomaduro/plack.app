import { Link, usePage } from '@inertiajs/react';
import { useEcho } from '@laravel/echo-react';
import { type PropsWithChildren, useState } from 'react';
import CreateWorkspaceDialog from '@/components/create-workspace-dialog';
import PendingInvitations from '@/components/pending-invitations';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { UserMenuContent } from '@/components/user-menu-content';
import { playMessageChime } from '@/lib/sound';
import { nickColorFor, handleFor, initialsFor } from '@/lib/user';
import { show as channelShow } from '@/routes/channel';
import {
    settings as workspaceSettings,
    show as workspaceShow,
} from '@/routes/workspace';

type Channel = {
    id: string;
    name: string;
    slug: string;
    unread_count: number;
    muted: boolean;
};

type WorkspaceSummary = {
    id: string;
    name: string;
    slug: string;
};

type Workspace = WorkspaceSummary & {
    channels: Channel[];
};

type WorkspaceLayoutProps = PropsWithChildren<{
    workspace: Workspace;
    workspaces?: WorkspaceSummary[];
    activeChannelSlug?: string;
    canManage?: boolean;
}>;

export default function WorkspaceLayout({
    workspace,
    workspaces = [],
    activeChannelSlug,
    canManage = false,
    children,
}: WorkspaceLayoutProps) {
    const { auth, pendingInvitations, pendingWorkspaceJoin } = usePage().props;
    const user = auth.user;

    const [createOpen, setCreateOpen] = useState(false);

    const others = workspaces.filter((w) => w.slug !== workspace.slug);

    const activeChannelId = workspace.channels.find(
        (c) => c.slug === activeChannelSlug,
    )?.id;

    // Ping when a message lands somewhere the user isn't looking — a channel
    // they aren't viewing, or any channel while the window isn't focused.
    useEcho<{ id: string; channel_id: string; user_id: string }>(
        `workspaces.${workspace.id}`,
        '.MessageCreated',
        ({ channel_id, user_id }) => {
            if (String(user_id) === String(user.id)) {
                return;
            }

            const target = workspace.channels.find((c) => c.id === channel_id);

            if (target?.muted) {
                return;
            }

            const isActive =
                channel_id === activeChannelId && document.hasFocus();

            if (!isActive) {
                playMessageChime();
            }
        },
    );

    return (
        <div className="flex h-screen bg-ink-900 font-mono text-fg">
            {/* ── sidebar ── */}
            <aside className="flex w-[250px] flex-none flex-col border-r border-line bg-ink-950">
                {/* workspace selector */}
                <div className="border-b border-line px-[18px] pt-4 pb-[15px]">
                    <div className="text-[9px] tracking-[.22em] text-mute uppercase">
                        workspace
                    </div>

                    <DropdownMenu>
                        <DropdownMenuTrigger className="mt-[7px] flex items-center gap-2 text-[13px] font-semibold text-amber outline-none">
                            {workspace.name}{' '}
                            <span className="font-normal text-mute">▾</span>
                        </DropdownMenuTrigger>
                        <DropdownMenuContent
                            align="start"
                            className="min-w-[214px] rounded-none border-line bg-ink-950 font-mono"
                        >
                            {others.map((w) => (
                                <DropdownMenuItem
                                    key={w.id}
                                    asChild
                                    className="rounded-none text-[12.5px] text-dim focus:bg-ink-800 focus:text-fg"
                                >
                                    <Link href={workspaceShow(w.slug)}>
                                        {w.name}
                                    </Link>
                                </DropdownMenuItem>
                            ))}

                            {(others.length > 0 || canManage) && (
                                <DropdownMenuSeparator className="bg-line" />
                            )}

                            {canManage && (
                                <DropdownMenuItem
                                    asChild
                                    className="rounded-none text-[12.5px] text-dim focus:bg-ink-800 focus:text-fg"
                                >
                                    <Link
                                        href={workspaceSettings(workspace.slug)}
                                        data-test="workspace-settings-link"
                                    >
                                        workspace settings
                                    </Link>
                                </DropdownMenuItem>
                            )}

                            <DropdownMenuItem
                                onSelect={(e) => {
                                    e.preventDefault();
                                    setCreateOpen(true);
                                }}
                                className="rounded-none text-[12.5px] text-amber focus:bg-ink-800 focus:text-amber"
                            >
                                + create workspace
                            </DropdownMenuItem>
                        </DropdownMenuContent>
                    </DropdownMenu>

                    <CreateWorkspaceDialog
                        open={createOpen}
                        onOpenChange={setCreateOpen}
                        trigger={null}
                    />
                </div>

                {/* channel list */}
                <nav className="flex-1 px-[14px] py-4">
                    <div className="mb-[10px] text-[9px] tracking-[.22em] text-mute uppercase">
                        channels
                    </div>

                    <div className="flex flex-col gap-[2px] text-[12.5px]">
                        {workspace.channels.map((channel) => {
                            const active = channel.slug === activeChannelSlug;
                            const unread = !active && channel.unread_count > 0;

                            return (
                                <Link
                                    key={channel.id}
                                    href={channelShow({
                                        workspace: workspace.slug,
                                        channel: channel.slug,
                                    })}
                                    className={
                                        active
                                            ? 'flex items-center gap-2 border-l-2 border-green bg-ink-800 px-2 py-[6px] text-fg'
                                            : 'flex items-center gap-2 px-2 py-[6px] text-dim transition-colors hover:text-fg'
                                    }
                                >
                                    <span
                                        className={
                                            unread
                                                ? 'flex-1 truncate font-semibold text-fg'
                                                : 'flex-1 truncate'
                                        }
                                    >
                                        # {channel.name}
                                    </span>

                                    {unread && (
                                        <span className="flex h-[16px] min-w-[16px] items-center justify-center rounded-full bg-green px-1 text-[9px] font-semibold text-ink-900">
                                            {channel.unread_count > 99
                                                ? '99+'
                                                : channel.unread_count}
                                        </span>
                                    )}
                                </Link>
                            );
                        })}
                    </div>

                    {(pendingInvitations.length > 0 ||
                        pendingWorkspaceJoin) && (
                        <div className="mt-6">
                            <div className="mb-[10px] text-[9px] tracking-[.22em] text-mute uppercase">
                                pending
                            </div>

                            <PendingInvitations
                                invitations={pendingInvitations}
                                workspaceJoin={pendingWorkspaceJoin}
                            />
                        </div>
                    )}
                </nav>

                {/* current user */}
                <DropdownMenu>
                    <DropdownMenuTrigger className="flex w-full items-center gap-[9px] border-t border-line px-4 py-3 text-xs text-dim transition-colors outline-none hover:text-fg data-[state=open]:bg-ink-800 data-[state=open]:text-fg">
                        <span
                            className="flex h-[22px] w-[22px] items-center justify-center text-[10px] font-semibold text-ink-900"
                            style={{ backgroundColor: nickColorFor(user.name) }}
                        >
                            {initialsFor(user.name)}
                        </span>
                        {handleFor(user.name)}
                        <span className="ml-auto text-[8px] text-green">●</span>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        align="start"
                        side="top"
                        className="min-w-[218px] rounded-none border-line bg-ink-950 font-mono"
                    >
                        <UserMenuContent user={user} />
                    </DropdownMenuContent>
                </DropdownMenu>
            </aside>

            {/* ── main ── */}
            <main className="flex min-w-0 flex-1 flex-col">{children}</main>
        </div>
    );
}
