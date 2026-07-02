import { Form, Head, router } from '@inertiajs/react';
import { useEcho } from '@laravel/echo-react';
import MessageController from '@/actions/App/Http/Controllers/MessageController';
import CreateChannelDialog from '@/components/create-channel-dialog';
import DeleteChannelDialog from '@/components/delete-channel-dialog';
import EditChannelDialog from '@/components/edit-channel-dialog';
import InputError from '@/components/input-error';
import WorkspaceLayout, {
    MobileSidebarToggle,
} from '@/layouts/workspace-layout';
import { nickColorFor } from '@/lib/user';

function messageTime(iso: string): string {
    return new Date(iso).toLocaleTimeString([], {
        hour: 'numeric',
        minute: '2-digit',
        hour12: false,
    });
}

type Channel = {
    id: string;
    name: string;
    slug: string;
};

type Message = {
    id: string;
    body: string;
    sender: string;
    createdAt: string;
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
    messages,
    workspaces,
    canManage = false,
}: {
    workspace: Workspace;
    channel: Channel;
    messages: Message[];
    workspaces?: WorkspaceSummary[];
    canManage?: boolean;
}) {
    useEcho(`channels.${channel.id}`, '.MessageCreated', () => {
        router.reload({ only: ['messages'] });
    });

    useEcho(
        `workspaces.${workspace.id}`,
        ['.ChannelCreated', '.ChannelDeleted'],
        () => {
            router.reload({ only: ['workspace'] });
        },
    );

    useEcho(`workspaces.${workspace.id}`, '.ChannelUpdated', () => {
        router.reload({ only: ['workspace', 'channel'] });
    });

    return (
        <WorkspaceLayout
            workspace={workspace}
            workspaces={workspaces}
            activeChannelSlug={channel.slug}
            canManage={canManage}
        >
            <Head title={channel.name} />

            {/* header */}
            <header className="flex items-center justify-between gap-3 border-b border-line px-4 py-[15px] md:px-6">
                <div className="flex min-w-0 items-center gap-3">
                    <MobileSidebarToggle />
                    <div className="flex min-w-0 items-baseline gap-3">
                        <span className="truncate text-[15px] font-semibold text-amber">
                            # {channel.name}
                        </span>
                        <span className="hidden text-[11px] text-mute sm:inline">
                            {workspace.name}
                        </span>
                    </div>
                </div>

                {canManage && (
                    <div className="flex flex-none items-center gap-1">
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
            <div className="flex flex-1 flex-col justify-end gap-[14px] overflow-y-auto px-4 py-[18px] text-[12.5px] leading-[1.55] md:px-6">
                {messages.length === 0 ? (
                    <div className="text-faint">
                        # no messages yet — say hello
                    </div>
                ) : (
                    messages.map((message) => (
                        <div key={message.id} className="break-words">
                            <span
                                style={{ color: nickColorFor(message.sender) }}
                            >
                                {message.sender}
                            </span>
                            <span className="mx-2 text-faint">
                                {messageTime(message.createdAt)}
                            </span>
                            <span className="text-fg">{message.body}</span>
                        </div>
                    ))
                )}
            </div>

            {/* composer */}
            <Form
                {...MessageController.store.form([
                    workspace.slug,
                    channel.slug,
                ])}
                options={{ preserveScroll: true }}
                resetOnSuccess
                className="mx-4 mb-4 md:mx-6 md:mb-5"
            >
                {({ errors }) => (
                    <>
                        <div className="flex items-center gap-2 border border-line px-[14px] py-[11px] text-[12.5px]">
                            <span className="text-green">&gt;</span>
                            <input
                                type="text"
                                name="body"
                                placeholder={`message #${channel.name}`}
                                autoComplete="off"
                                className="min-w-0 flex-1 bg-transparent text-fg caret-green outline-none placeholder:text-faint"
                            />
                        </div>

                        <InputError message={errors.body} className="mt-2" />
                    </>
                )}
            </Form>
        </WorkspaceLayout>
    );
}
