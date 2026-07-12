import {
    type ChangeEvent,
    type KeyboardEvent,
    forwardRef,
    useCallback,
    useEffect,
    useImperativeHandle,
    useRef,
    useState,
} from 'react';

type Member = { name: string; email: string };

type MentionInputProps = {
    name: string;
    placeholder?: string;
    disabled?: boolean;
    members: Member[];
    onChange?: () => void;
    className?: string;
};

const MAX_RESULTS = 5;

/**
 * Extract the @-mention query at the cursor position.
 * Returns the text after `@` (may be empty string for bare `@`), or null if
 * the cursor is not inside an @-mention token.
 */
function getMentionQuery(
    value: string,
    cursorPos: number,
): { query: string; start: number; end: number } | null {
    const before = value.slice(0, cursorPos);
    const match = before.match(/(^|[\s])@([\w.]*)$/);
    if (!match) return null;

    const query = match[2];
    const start = before.length - query.length - 1;
    return { query, start, end: cursorPos };
}

/**
 * Turn a display name into a mentionable slug: "John Doe" → "John.Doe"
 */
function mentionSlug(displayName: string): string {
    return displayName.trim().replace(/\s+/g, '.');
}

const MentionInput = forwardRef<HTMLInputElement, MentionInputProps>(
    function MentionInput(
        { name, placeholder, disabled, members, onChange, className },
        ref,
    ) {
        const inputRef = useRef<HTMLInputElement>(null);
        useImperativeHandle(ref, () => inputRef.current!);

        const [value, setValue] = useState('');
        const [matches, setMatches] = useState<Member[]>([]);
        const [selectedIndex, setSelectedIndex] = useState(0);
        const [open, setOpen] = useState(false);

        const updateMatches = useCallback(
            (input: HTMLInputElement) => {
                const mention = getMentionQuery(
                    input.value,
                    input.selectionStart ?? input.value.length,
                );

                if (!mention) {
                    setOpen(false);
                    setMatches([]);
                    return;
                }

                const q = mention.query.toLowerCase();
                const filtered = members
                    .filter(
                        (m) =>
                            m.name.toLowerCase().startsWith(q) ||
                            m.email.toLowerCase().startsWith(q) ||
                            mentionSlug(m.name).toLowerCase().startsWith(q),
                    )
                    .slice(0, MAX_RESULTS);

                setMatches(filtered);
                setSelectedIndex(0);
                setOpen(filtered.length > 0);
            },
            [members],
        );

        const insertMention = useCallback((member: Member) => {
            const input = inputRef.current;
            if (!input) return;

            const mention = getMentionQuery(
                input.value,
                input.selectionStart ?? input.value.length,
            );
            if (!mention) return;

            const before = input.value.slice(0, mention.start);
            const after = input.value.slice(mention.end);
            const inserted = `@${mentionSlug(member.name)} `;
            const newValue = before + inserted + after;

            setValue(newValue);
            setOpen(false);
            setMatches([]);

            const cursorPos = before.length + inserted.length;
            requestAnimationFrame(() => {
                input.focus();
                input.setSelectionRange(cursorPos, cursorPos);
            });
        }, []);

        const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
            setValue(e.target.value);
            updateMatches(e.target);
            onChange?.();
        };

        const handleKeyDown = (e: KeyboardEvent<HTMLInputElement>) => {
            if (!open) return;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                setSelectedIndex((i) => (i + 1) % matches.length);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                setSelectedIndex(
                    (i) => (i - 1 + matches.length) % matches.length,
                );
            } else if (e.key === 'Enter' || e.key === 'Tab') {
                e.preventDefault();
                insertMention(matches[selectedIndex]);
            } else if (e.key === 'Escape') {
                e.preventDefault();
                setOpen(false);
            }
        };

        const blurTimeout = useRef<ReturnType<typeof setTimeout>>();
        const handleBlur = () => {
            blurTimeout.current = setTimeout(() => setOpen(false), 150);
        };
        const handleFocus = () => {
            clearTimeout(blurTimeout.current);
        };

        useEffect(() => {
            return () => clearTimeout(blurTimeout.current);
        }, []);

        // Reset value when form resets (Inertia resetOnSuccess)
        useEffect(() => {
            const input = inputRef.current;
            if (!input) return;

            const form = input.closest('form');
            if (!form) return;

            const handleReset = () => setValue('');
            form.addEventListener('reset', handleReset);
            return () => form.removeEventListener('reset', handleReset);
        }, []);

        return (
            <div className="relative flex-1">
                <input type="hidden" name={name} value={value} />

                {open && matches.length > 0 && (
                    <div className="absolute bottom-full left-0 z-10 mb-1 w-64 border border-border bg-background">
                        {matches.map((member, i) => (
                            <button
                                key={member.email}
                                type="button"
                                onMouseDown={(e) => {
                                    e.preventDefault();
                                    insertMention(member);
                                }}
                                className={`flex w-full items-center gap-2 px-3 py-1.5 text-left text-[12.5px] ${
                                    i === selectedIndex
                                        ? 'bg-primary/10 text-primary'
                                        : 'text-foreground hover:bg-primary/5'
                                }`}
                            >
                                <span className="font-medium">
                                    {member.name}
                                </span>
                                <span className="truncate text-muted-foreground">
                                    {member.email}
                                </span>
                            </button>
                        ))}
                    </div>
                )}

                <input
                    ref={inputRef}
                    type="text"
                    value={value}
                    placeholder={placeholder}
                    autoComplete="off"
                    disabled={disabled}
                    onChange={handleChange}
                    onKeyDown={handleKeyDown}
                    onBlur={handleBlur}
                    onFocus={handleFocus}
                    className={className}
                />
            </div>
        );
    },
);

export default MentionInput;
