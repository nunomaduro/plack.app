import { Link, usePage } from '@inertiajs/react';
import { Check, ChevronsUpDown, Plus } from 'lucide-react';
import { useState } from 'react';
import CreateWorkspaceDialog from '@/components/create-workspace-dialog';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import {
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
    useSidebar,
} from '@/components/ui/sidebar';
import { useIsMobile } from '@/hooks/use-mobile';
import { show } from '@/routes/workspace';

export function WorkspaceSwitcher() {
    const { navWorkspaces, currentWorkspace } = usePage().props;
    const { state } = useSidebar();
    const isMobile = useIsMobile();
    const [createOpen, setCreateOpen] = useState(false);

    return (
        <SidebarMenu>
            <SidebarMenuItem>
                <DropdownMenu>
                    <DropdownMenuTrigger asChild>
                        <SidebarMenuButton
                            size="lg"
                            className="data-[state=open]:bg-sidebar-accent data-[state=open]:text-sidebar-accent-foreground"
                        >
                            <span className="truncate font-medium">
                                {currentWorkspace?.name ?? 'Select workspace'}
                            </span>
                            <ChevronsUpDown className="ml-auto size-4" />
                        </SidebarMenuButton>
                    </DropdownMenuTrigger>
                    <DropdownMenuContent
                        className="w-(--radix-dropdown-menu-trigger-width) min-w-56 rounded-lg"
                        align="start"
                        side={
                            isMobile
                                ? 'bottom'
                                : state === 'collapsed'
                                  ? 'right'
                                  : 'bottom'
                        }
                    >
                        <DropdownMenuLabel className="text-xs text-muted-foreground">
                            Workspaces
                        </DropdownMenuLabel>

                        {navWorkspaces.map((workspace) => (
                            <DropdownMenuItem key={workspace.id} asChild>
                                <Link
                                    href={show(workspace.slug)}
                                    className="cursor-pointer"
                                >
                                    <span className="truncate">
                                        {workspace.name}
                                    </span>

                                    {workspace.id === currentWorkspace?.id && (
                                        <Check className="ml-auto size-4" />
                                    )}
                                </Link>
                            </DropdownMenuItem>
                        ))}

                        <DropdownMenuSeparator />

                        <DropdownMenuItem
                            className="cursor-pointer"
                            onSelect={() => setCreateOpen(true)}
                        >
                            <Plus className="size-4" />
                            Create workspace
                        </DropdownMenuItem>
                    </DropdownMenuContent>
                </DropdownMenu>
            </SidebarMenuItem>

            <CreateWorkspaceDialog
                open={createOpen}
                onOpenChange={setCreateOpen}
            />
        </SidebarMenu>
    );
}
