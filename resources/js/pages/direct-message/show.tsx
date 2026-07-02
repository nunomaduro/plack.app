import { Head, router, usePage } from '@inertiajs/react';
import type { FormEvent } from 'react';
import { useRef } from 'react';
import Heading from '@/components/heading';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import AppLayout from '@/layouts/app-layout';
import directMessage, { index, show } from '@/routes/direct-message';
import type { BreadcrumbItem } from '@/types';

type Participant = {
    id: string;
    name: string;
};

type DirectMessageItem = {
    id: string;
    body: string;
    sender: { id: string; name: string };
    created_at: string;
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
};

export default function DirectMessageShow({
    conversation,
    messages,
    otherParticipant,
}: {
    conversation: { id: string };
    messages: Paginated<DirectMessageItem>;
    otherParticipant: Participant | null;
}) {
    const { auth } = usePage().props;
    const inputRef = useRef<HTMLInputElement>(null);

    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Messages',
            href: index(),
        },
        {
            title: otherParticipant?.name ?? 'Conversation',
            href: show(conversation.id),
        },
    ];

    function handleSend(e: FormEvent<HTMLFormElement>) {
        e.preventDefault();

        const form = e.currentTarget;
        const formData = new FormData(form);
        const body = formData.get('body') as string;

        if (!body?.trim()) {
            return;
        }

        router.post(
            directMessage.message.store(conversation.id),
            { body },
            {
                preserveScroll: true,
                onSuccess: () => {
                    form.reset();
                    inputRef.current?.focus();
                },
            },
        );
    }

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={otherParticipant?.name ?? 'Conversation'} />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading
                        title={otherParticipant?.name ?? 'Conversation'}
                        description="Direct message conversation."
                    />
                </div>

                <div className="flex flex-1 flex-col gap-4 overflow-y-auto">
                    {messages.data.length === 0 ? (
                        <div className="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 p-12 dark:border-sidebar-border">
                            <p className="text-sm text-muted-foreground">
                                No messages yet. Start the conversation!
                            </p>
                        </div>
                    ) : (
                        <div className="flex flex-col gap-3">
                            {[...messages.data].reverse().map((msg) => {
                                const isMine =
                                    String(msg.sender.id) ===
                                    String(auth.user.id);

                                return (
                                    <div
                                        key={msg.id}
                                        className={`flex ${isMine ? 'justify-end' : 'justify-start'}`}
                                    >
                                        <div
                                            className={`max-w-[70%] rounded-xl px-4 py-2 ${
                                                isMine
                                                    ? 'bg-primary text-primary-foreground'
                                                    : 'bg-muted'
                                            }`}
                                        >
                                            <p className="text-sm">
                                                {msg.body}
                                            </p>

                                            <p className="mt-1 text-xs opacity-70">
                                                {msg.sender.name}
                                            </p>
                                        </div>
                                    </div>
                                );
                            })}
                        </div>
                    )}

                    {messages.last_page > 1 && messages.next_page_url && (
                        <div className="flex justify-center">
                            <Button
                                variant="outline"
                                size="sm"
                                onClick={() =>
                                    router.get(messages.next_page_url!)
                                }
                            >
                                Load older messages
                            </Button>
                        </div>
                    )}
                </div>

                <form onSubmit={handleSend} className="flex items-center gap-2">
                    <Input
                        ref={inputRef}
                        name="body"
                        placeholder="Type a message..."
                        className="flex-1"
                        autoComplete="off"
                    />

                    <Button type="submit">Send</Button>
                </form>
            </div>
        </AppLayout>
    );
}
