import { Link, router, usePage } from '@inertiajs/react';
import { useEcho } from '@laravel/echo-react';
import { Menu, Pencil, Plus, Trash2 } from 'lucide-react';
import {
    createContext,
    type PropsWithChildren,
    useContext,
    useEffect,
    useState,
} from 'react';
import CreateChannelDialog from '@/components/create-channel-dialog';
import CreateWorkspaceDialog from '@/components/create-workspace-dialog';
import DeleteChannelDialog from '@/components/delete-channel-dialog';
import EditChannelDialog from '@/components/edit-channel-dialog';
import PendingInvitations from '@/components/pending-invitations';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Sheet, SheetContent, SheetTitle } from '@/components/ui/sheet';
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

const MobileSidebarContext = createContext<(open: boolean) => void>(() => {});

export function MobileSidebarToggle() {
    const setOpen = useContext(MobileSidebarContext);

    return (
        <button
            type="button"
            onClick={() => setOpen(true)}
            aria-label="Open navigation"
            data-test="mobile-sidebar-toggle"
            className="flex-none text-muted-foreground transition-colors hover:text-foreground md:hidden"
        >
            <Menu className="h-[18px] w-[18px]" />
        </button>
    );
}

function SidebarContent({
    workspace,
    others,
    activeChannelSlug,
    canManage,
    variant,
    onCreateWorkspace,
}: {
    workspace: Workspace;
    others: WorkspaceSummary[];
    activeChannelSlug?: string;
    canManage: boolean;
    variant: 'desktop' | 'mobile';
    onCreateWorkspace: () => void;
}) {
    const { auth, pendingInvitations, pendingWorkspaceJoin } = usePage().props;
    const user = auth.user;

    return (
        <>
            {/* workspace selector */}
            <div className="border-b border-border px-[18px] pt-4 pb-[15px]">
                <div className="text-[9px] tracking-[.22em] text-muted-foreground uppercase">
                    workspace
                </div>

                <DropdownMenu>
                    <DropdownMenuTrigger className="mt-[7px] flex items-center gap-2 text-[13px] font-semibold text-primary outline-none">
                        {workspace.name}{' '}
                        <span className="font-normal text-muted-foreground">▾</span>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        align="start"
                        className="min-w-[214px] rounded-none border-border bg-popover font-mono"
                    >
                        {others.map((w) => (
                            <DropdownMenuItem
                                key={w.id}
                                asChild
                                className="rounded-none text-[12.5px] text-muted-foreground focus:bg-accent focus:text-foreground"
                            >
                                <Link href={workspaceShow(w.slug)}>
                                    {w.name}
                                </Link>
                            </DropdownMenuItem>
                        ))}

                        {(others.length > 0 || canManage) && (
                            <DropdownMenuSeparator className="bg-border" />
                        )}

                        {canManage && (
                            <DropdownMenuItem
                                asChild
                                className="rounded-none text-[12.5px] text-muted-foreground focus:bg-accent focus:text-foreground"
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
                                onCreateWorkspace();
                            }}
                            className="rounded-none text-[12.5px] text-primary focus:bg-accent focus:text-primary"
                        >
                            + create workspace
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </div>

            {/* channel list */}
            <nav className="flex-1 overflow-y-auto px-[14px] py-4">
                <div className="mb-[10px] flex items-center justify-between">
                    <div className="text-[9px] tracking-[.22em] text-muted-foreground uppercase">
                        channels
                    </div>

                    {canManage && (
                        <CreateChannelDialog
                            workspaceSlug={workspace.slug}
                            trigger={
                                <button
                                    type="button"
                                    aria-label="New channel"
                                    data-test="create-channel-trigger"
                                    className="text-muted-foreground transition-colors hover:text-primary"
                                >
                                    <Plus className="size-3.5" />
                                </button>
                            }
                        />
                    )}
                </div>

                <div className="flex flex-col gap-[2px] text-[12.5px]">
                    {workspace.channels.map((channel) => {
                        const active = channel.slug === activeChannelSlug;
                        const unread = !active && channel.unread_count > 0;

                        return (
                            <div
                                key={channel.id}
                                className={
                                    active
                                        ? 'group flex items-center gap-1 border-l-2 border-primary bg-accent px-2 py-[6px]'
                                        : 'group flex items-center gap-1 px-2 py-[6px]'
                                }
                            >
                                <Link
                                    href={channelShow({
                                        workspace: workspace.slug,
                                        channel: channel.slug,
                                    })}
                                    data-test={`${variant}-channel-${channel.slug}`}
                                    className={
                                        active
                                            ? 'min-w-0 flex-1 truncate text-foreground'
                                            : unread
                                              ? 'min-w-0 flex-1 truncate font-semibold text-foreground'
                                              : 'min-w-0 flex-1 truncate text-muted-foreground transition-colors hover:text-foreground'
                                    }
                                >
                                    # {channel.name}
                                </Link>

                                {unread && (
                                    <span className="flex h-[16px] min-w-[16px] items-center justify-center rounded-full bg-green px-1 text-[9px] font-semibold text-primary-foreground">
                                        {channel.unread_count > 99
                                            ? '99+'
                                            : channel.unread_count}
                                    </span>
                                )}

                                {canManage && (
                                    <div className="flex items-center gap-[6px] opacity-0 transition-opacity group-focus-within:opacity-100 group-hover:opacity-100">
                                        <EditChannelDialog
                                            workspaceSlug={workspace.slug}
                                            channel={channel}
                                            trigger={
                                                <button
                                                    type="button"
                                                    aria-label={`Edit #${channel.name}`}
                                                    data-test={`edit-channel-trigger-${channel.slug}`}
                                                    className="text-muted-foreground transition-colors hover:text-foreground"
                                                >
                                                    <Pencil className="size-3" />
                                                </button>
                                            }
                                        />

                                        <DeleteChannelDialog
                                            workspaceSlug={workspace.slug}
                                            channel={channel}
                                            trigger={
                                                <button
                                                    type="button"
                                                    aria-label={`Delete #${channel.name}`}
                                                    data-test={`delete-channel-trigger-${channel.slug}`}
                                                    className="text-muted-foreground transition-colors hover:text-destructive"
                                                >
                                                    <Trash2 className="size-3" />
                                                </button>
                                            }
                                        />
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>

                {(pendingInvitations.length > 0 || pendingWorkspaceJoin) && (
                    <div className="mt-6">
                        <div className="mb-[10px] text-[9px] tracking-[.22em] text-muted-foreground uppercase">
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
                <DropdownMenuTrigger className="flex w-full items-center gap-[9px] border-t border-border px-4 py-3 text-xs text-muted-foreground transition-colors outline-none hover:text-foreground data-[state=open]:bg-accent data-[state=open]:text-foreground">
                    <span
                        className="flex h-[22px] w-[22px] items-center justify-center text-[10px] font-semibold text-primary-foreground"
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
                    className="min-w-[218px] rounded-none border-border bg-popover font-mono"
                >
                    <UserMenuContent user={user} />
                </DropdownMenuContent>
            </DropdownMenu>
        </>
    );
}

export default function WorkspaceLayout({
    workspace,
    workspaces = [],
    activeChannelSlug,
    canManage = false,
    children,
}: WorkspaceLayoutProps) {
    const { auth } = usePage().props;
    const user = auth.user;

    const [createOpen, setCreateOpen] = useState(false);
    const [mobileOpen, setMobileOpen] = useState(false);

    useEffect(() => router.on('navigate', () => setMobileOpen(false)), []);

    const others = workspaces.filter((w) => w.slug !== workspace.slug);

    const sidebarProps = {
        workspace,
        others,
        activeChannelSlug,
        canManage,
        onCreateWorkspace: () => setCreateOpen(true),
    };

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
        <MobileSidebarContext.Provider value={setMobileOpen}>
            <div className="flex h-screen bg-background font-mono text-foreground">
                {/* ── sidebar (desktop) ── */}
                <aside className="hidden w-[250px] flex-none flex-col border-r border-border bg-sidebar md:flex">
                    <SidebarContent {...sidebarProps} variant="desktop" />
                </aside>

                {/* ── sidebar (mobile drawer) ── */}
                <Sheet open={mobileOpen} onOpenChange={setMobileOpen}>
                    <SheetContent
                        side="left"
                        aria-describedby={undefined}
                        data-test="mobile-sidebar"
                        className="flex w-[280px] flex-col gap-0 rounded-none border-border bg-sidebar p-0 font-mono text-foreground md:hidden"
                    >
                        <SheetTitle className="sr-only">navigation</SheetTitle>
                        <SidebarContent {...sidebarProps} variant="mobile" />
                    </SheetContent>
                </Sheet>

                <CreateWorkspaceDialog
                    open={createOpen}
                    onOpenChange={setCreateOpen}
                    trigger={null}
                />

                {/* ── main ── */}
                <main className="flex min-w-0 flex-1 flex-col">{children}</main>
            </div>
        </MobileSidebarContext.Provider>
    );
}
