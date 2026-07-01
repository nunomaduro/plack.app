import { usePage } from '@inertiajs/react';
import { NavChannels } from '@/components/nav-channels';
import { NavUser } from '@/components/nav-user';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
} from '@/components/ui/sidebar';
import { WorkspaceSwitcher } from '@/components/workspace-switcher';

export function AppSidebar() {
    const { currentWorkspace } = usePage().props;

    return (
        <Sidebar collapsible="icon" variant="inset">
            <SidebarHeader>
                <WorkspaceSwitcher />
            </SidebarHeader>

            <SidebarContent>
                {currentWorkspace && (
                    <NavChannels workspace={currentWorkspace} />
                )}
            </SidebarContent>

            <SidebarFooter>
                <NavUser />
            </SidebarFooter>
        </Sidebar>
    );
}
