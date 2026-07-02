import { Head } from '@inertiajs/react';
import CreateChannelDialog from '@/components/create-channel-dialog';
import DeleteChannelDialog from '@/components/delete-channel-dialog';
import EditChannelDialog from '@/components/edit-channel-dialog';
import WorkspaceLayout from '@/layouts/workspace-layout';

type Channel = {
    id: string;
    name: string;
    slug: string;
};

type WorkspaceSummary = {
    id: string;
    name: string;
    slug: string;
};

type Workspace = WorkspaceSummary & {
    channels: Channel[];
};

export default function ChannelShow({
    workspace,
    channel,
    workspaces,
    canManage = false,
}: {
    workspace: Workspace;
    channel: Channel;
    workspaces?: WorkspaceSummary[];
    canManage?: boolean;
}) {
    return (
        <WorkspaceLayout
            workspace={workspace}
            workspaces={workspaces}
            activeChannelSlug={channel.slug}
            canManage={canManage}
        >
            <Head title={channel.name} />

            {/* header */}
            <header className="flex items-center justify-between gap-3 border-b border-line px-6 py-[15px]">
                <div className="flex items-baseline gap-3">
                    <span className="text-[15px] font-semibold text-amber">
                        # {channel.name}
                    </span>
                    <span className="text-[11px] text-mute">
                        {workspace.name}
                    </span>
                </div>

                {canManage && (
                    <div className="flex items-center gap-1">
                        <CreateChannelDialog workspaceSlug={workspace.slug} />
                        <EditChannelDialog
                            workspaceSlug={workspace.slug}
                            channel={channel}
                        />
                        <DeleteChannelDialog
                            workspaceSlug={workspace.slug}
                            channel={channel}
                        />
                    </div>
                )}
            </header>

            {/* message log — bottom-anchored */}
            <div className="flex flex-1 flex-col justify-end gap-[14px] overflow-y-auto px-6 py-[18px] text-[12.5px] leading-[1.55]">
                <div className="text-faint"># no messages yet — say hello</div>
            </div>

            {/* composer — visual only */}
            <form
                onSubmit={(e) => e.preventDefault()}
                className="mx-6 mb-5 flex items-center gap-2 border border-line px-[14px] py-[11px] text-[12.5px] text-faint"
            >
                <span className="text-green">&gt;</span>
                <input
                    type="text"
                    placeholder={`message #${channel.name}`}
                    className="min-w-0 flex-1 bg-transparent text-fg caret-green outline-none placeholder:text-faint"
                />
            </form>
        </WorkspaceLayout>
    );
}
