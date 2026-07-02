import { Head, Link } from '@inertiajs/react';
import Heading from '@/components/heading';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import AppLayout from '@/layouts/app-layout';
import { index, show } from '@/routes/direct-message';
import type { BreadcrumbItem } from '@/types';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Messages',
        href: index(),
    },
];

type Participant = {
    id: string;
    name: string;
};

type Conversation = {
    id: string;
    participants: Participant[];
    messages: { body: string; created_at: string }[];
    created_at: string;
};

type Paginated<T> = {
    data: T[];
    current_page: number;
    last_page: number;
    prev_page_url: string | null;
    next_page_url: string | null;
};

export default function DirectMessageIndex({
    conversations,
}: {
    conversations: Paginated<Conversation>;
}) {
    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Messages" />

            <div className="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
                <div className="flex items-center justify-between">
                    <Heading
                        title="Messages"
                        description="Your direct messages."
                    />
                </div>

                {conversations.data.length === 0 ? (
                    <div className="flex flex-1 flex-col items-center justify-center gap-4 rounded-xl border border-dashed border-sidebar-border/70 p-12 dark:border-sidebar-border">
                        <p className="text-sm text-muted-foreground">
                            No messages yet.
                        </p>
                    </div>
                ) : (
                    <ul className="flex flex-col gap-2">
                        {conversations.data.map((conversation) => {
                            const other = conversation.participants[0];
                            const lastMessage = conversation.messages[0];

                            return (
                                <li
                                    key={conversation.id}
                                    className="flex items-center justify-between rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                                >
                                    <Link
                                        href={show(conversation.id)}
                                        className="flex flex-col gap-1"
                                    >
                                        <span className="font-medium hover:underline">
                                            {other?.name ?? 'Unknown'}
                                        </span>

                                        {lastMessage && (
                                            <span className="text-sm text-muted-foreground line-clamp-1">
                                                {lastMessage.body}
                                            </span>
                                        )}
                                    </Link>
                                </li>
                            );
                        })}
                    </ul>
                )}

                {conversations.last_page > 1 && (
                    <div className="flex items-center justify-between">
                        <Button
                            asChild
                            variant="outline"
                            size="sm"
                            disabled={!conversations.prev_page_url}
                        >
                            {conversations.prev_page_url ? (
                                <Link href={conversations.prev_page_url}>
                                    Previous
                                </Link>
                            ) : (
                                <span>Previous</span>
                            )}
                        </Button>

                        <span className="text-sm text-muted-foreground">
                            Page {conversations.current_page} of{' '}
                            {conversations.last_page}
                        </span>

                        <Button
                            asChild
                            variant="outline"
                            size="sm"
                            disabled={!conversations.next_page_url}
                        >
                            {conversations.next_page_url ? (
                                <Link href={conversations.next_page_url}>
                                    Next
                                </Link>
                            ) : (
                                <span>Next</span>
                            )}
                        </Button>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
