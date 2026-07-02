import { router } from '@inertiajs/react';
import { Plus, Search } from 'lucide-react';
import { useRef, useState } from 'react';
import DirectMessageController from '@/actions/App/Http/Controllers/DirectMessageController';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { Button } from '@/components/ui/button';
import {
    Dialog,
    DialogClose,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogTitle,
    DialogTrigger,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { search as searchUsers } from '@/routes/user';

type SearchUser = {
    id: string;
    name: string;
};

export default function StartConversationDialog() {
    const [open, setOpen] = useState(false);
    const [query, setQuery] = useState('');
    const [results, setResults] = useState<SearchUser[]>([]);
    const [searching, setSearching] = useState(false);
    const timerRef = useRef<ReturnType<typeof setTimeout>>(undefined);

    function handleSearch(value: string) {
        setQuery(value);

        if (timerRef.current) {
            clearTimeout(timerRef.current);
        }

        if (value.length < 2) {
            setResults([]);
            return;
        }

        setSearching(true);

        timerRef.current = setTimeout(async () => {
            try {
                const res = await fetch(
                    searchUsers.url({ query: { q: value } }),
                );
                const data = await res.json();
                setResults(data);
            } catch {
                setResults([]);
            } finally {
                setSearching(false);
            }
        }, 300);
    }

    function startConversation(user: SearchUser) {
        router.post(
            DirectMessageController.store.url(),
            { user_id: user.id },
            {
                onSuccess: () => setOpen(false),
            },
        );
    }

    return (
        <Dialog open={open} onOpenChange={setOpen}>
            <DialogTrigger asChild>
                <Button data-test="start-conversation-trigger">
                    <Plus />
                    New message
                </Button>
            </DialogTrigger>
            <DialogContent data-test="start-conversation-dialog">
                <DialogTitle>New message</DialogTitle>
                <DialogDescription>
                    Search for a user to start a conversation.
                </DialogDescription>

                <div className="space-y-4">
                    <div className="relative">
                        <Search className="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground" />

                        <Input
                            value={query}
                            onChange={(e) => handleSearch(e.target.value)}
                            placeholder="Search users..."
                            className="pl-9"
                            autoComplete="off"
                            autoFocus
                        />
                    </div>

                    {searching && (
                        <p className="text-sm text-muted-foreground">
                            Searching...
                        </p>
                    )}

                    {!searching && results.length > 0 && (
                        <ul className="flex flex-col gap-1">
                            {results.map((user) => (
                                <li key={user.id}>
                                    <button
                                        type="button"
                                        onClick={() => startConversation(user)}
                                        className="flex w-full items-center gap-3 rounded-lg px-3 py-2 text-left hover:bg-muted"
                                        data-test={`user-result-${user.id}`}
                                    >
                                        <Avatar className="size-8">
                                            <AvatarFallback>
                                                {user.name
                                                    .split(' ')
                                                    .map((n) => n[0])
                                                    .join('')
                                                    .toUpperCase()
                                                    .slice(0, 2)}
                                            </AvatarFallback>
                                        </Avatar>

                                        <div className="flex flex-col">
                                            <span className="text-sm font-medium">
                                                {user.name}
                                            </span>
                                        </div>
                                    </button>
                                </li>
                            ))}
                        </ul>
                    )}

                    {!searching &&
                        query.length >= 2 &&
                        results.length === 0 && (
                            <p className="text-sm text-muted-foreground">
                                No users found.
                            </p>
                        )}
                </div>

                <DialogFooter className="gap-2">
                    <DialogClose asChild>
                        <Button variant="secondary">Cancel</Button>
                    </DialogClose>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    );
}
