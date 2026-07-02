import { Form, Head, router, useHttp } from '@inertiajs/react';
import { useEcho, useEchoPresence } from '@laravel/echo-react';
import { useEffect, useRef, useState } from 'react';
import ChannelTypingController from '@/actions/App/Http/Controllers/ChannelTypingController';
import MessageController from '@/actions/App/Http/Controllers/MessageController';
import CreateChannelDialog from '@/components/create-channel-dialog';
import DeleteChannelDialog from '@/components/delete-channel-dialog';
import EditChannelDialog from '@/components/edit-channel-dialog';
import InputError from '@/components/input-error';
import WorkspaceLayout, {
    MobileSidebarToggle,
} from '@/layouts/workspace-layout';
import { renderMarkdown } from '@/lib/markdown';
import { nickColorFor } from '@/lib/user';

const TYPING_THROTTLE_MS = 2000;
const TYPING_GRACE_MS = 3500;

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

type SidebarChannel = Channel & {
    unread_count: number;
    muted: boolean;
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
    channels: SidebarChannel[];
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
    const logRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        const log = logRef.current;
        if (log) {
            log.scrollTop = log.scrollHeight;
        }
    }, [messages]);

    const [typingUsers, setTypingUsers] = useState<Record<string, string>>({});
    const typingTimeouts = useRef<
        Record<string, ReturnType<typeof setTimeout>>
    >({});
    const lastTypingSentAt = useRef(0);
    const { post: postTyping } = useHttp({});

    useEcho(`channels.${channel.id}`, '.MessageCreated', () => {
        router.reload({ only: ['messages'] });
    });

    useEcho(
        `workspaces.${workspace.id}`,
        ['.ChannelCreated', '.ChannelDeleted', '.MessageCreated'],
        () => {
            router.reload({ only: ['workspace'] });
        },
    );

    useEcho(`workspaces.${workspace.id}`, '.ChannelUpdated', () => {
        router.reload({ only: ['workspace', 'channel'] });
    });

    useEchoPresence<{ id: string; name: string }>(
        `channels.${channel.id}`,
        '.UserTyping',
        (event) => {
            setTypingUsers((current) => ({
                ...current,
                [event.id]: event.name,
            }));

            clearTimeout(typingTimeouts.current[event.id]);
            typingTimeouts.current[event.id] = setTimeout(() => {
                delete typingTimeouts.current[event.id];
                setTypingUsers((current) => {
                    const next = { ...current };
                    delete next[event.id];
                    return next;
                });
            }, TYPING_GRACE_MS);
        },
    );

    useEffect(() => {
        const timeouts = typingTimeouts.current;

        return () => {
            Object.values(timeouts).forEach(clearTimeout);
            typingTimeouts.current = {};
            setTypingUsers({});
        };
    }, [channel.id]);

    const sendTyping = () => {
        const now = Date.now();
        if (now - lastTypingSentAt.current < TYPING_THROTTLE_MS) {
            return;
        }
        lastTypingSentAt.current = now;

        void postTyping(
            ChannelTypingController.url([workspace.slug, channel.slug]),
        ).catch(() => {
            // typing is fire-and-forget
        });
    };

    const typingNames = Object.values(typingUsers);

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
            <div ref={logRef} className="flex-1 overflow-y-auto">
                <div className="flex min-h-full flex-col justify-end gap-[14px] px-4 py-[18px] text-[12.5px] leading-[1.55] md:px-6">
                    {messages.length === 0 ? (
                        <div className="text-faint">
                            # no messages yet — say hello
                        </div>
                    ) : (
                        messages.map((message) => (
                            <div key={message.id} className="break-words">
                                <span
                                    style={{
                                        color: nickColorFor(message.sender),
                                    }}
                                >
                                    {message.sender}
                                </span>
                                <span className="mx-2 text-faint">
                                    {messageTime(message.createdAt)}
                                </span>
                                <span className="text-fg">
                                    {renderMarkdown(message.body)}
                                </span>
                            </div>
                        ))
                    )}
                </div>
            </div>

            {/* typing indicator */}
            <div
                className="mx-6 mb-1 h-4 text-[11px] text-mute"
                aria-live="polite"
            >
                {typingNames.length > 0 &&
                    `${typingNames.join(', ')} ${typingNames.length === 1 ? 'is' : 'are'} typing ...`}
            </div>

            {/* composer */}
            <Form
                {...MessageController.store.form([
                    workspace.slug,
                    channel.slug,
                ])}
                options={{ preserveScroll: true }}
                resetOnSuccess
                onFinish={() => setTimeout(() => inputRef.current?.focus(), 0)}
                className="mx-4 mb-4 md:mx-6 md:mb-5"
            >
                {({ errors, processing }) => (
                    <>
                        <div className="flex items-center gap-2 border border-line px-[14px] py-[11px] text-[12.5px]">
                            <span className="text-green">&gt;</span>
                            <input
                                ref={inputRef}
                                type="text"
                                name="body"
                                placeholder={`message #${channel.name}`}
                                autoComplete="off"
                                disabled={processing}
                                onChange={sendTyping}
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
