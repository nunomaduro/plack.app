import { Link } from '@inertiajs/react';
import { Hash, Plus } from 'lucide-react';
import { useState } from 'react';
import CreateChannelDialog from '@/components/create-channel-dialog';
import {
    SidebarGroup,
    SidebarGroupAction,
    SidebarGroupLabel,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { useCurrentUrl } from '@/hooks/use-current-url';
import { show as channelShow } from '@/routes/channel';
import type { CurrentWorkspace } from '@/types';

export function NavChannels({ workspace }: { workspace: CurrentWorkspace }) {
    const { isCurrentUrl } = useCurrentUrl();
    const [createOpen, setCreateOpen] = useState(false);

    return (
        <SidebarGroup>
            <SidebarGroupLabel>Channels</SidebarGroupLabel>

            <SidebarGroupAction
                title="Create channel"
                aria-label="Create channel"
                onClick={() => setCreateOpen(true)}
            >
                <Plus />
            </SidebarGroupAction>

            <SidebarMenu>
                {workspace.channels.map((channel) => {
                    const href = channelShow({
                        workspace: workspace.slug,
                        channel: channel.slug,
                    });

                    return (
                        <SidebarMenuItem key={channel.id}>
                            <SidebarMenuButton
                                asChild
                                isActive={isCurrentUrl(href)}
                                tooltip={{ children: channel.name }}
                            >
                                <Link href={href} prefetch>
                                    <Hash />
                                    <span className="truncate">
                                        {channel.name}
                                    </span>
                                </Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    );
                })}
            </SidebarMenu>

            <CreateChannelDialog
                workspaceSlug={workspace.slug}
                open={createOpen}
                onOpenChange={setCreateOpen}
            />
        </SidebarGroup>
    );
}
