/**
 * Frontend derivations for the terminal UI: the backend User only carries
 * name/email/id, but the design shows lowercase handles, initials avatars, and
 * a per-user color. These derive that look deterministically so the same user
 * always renders identically (design rule: "hash the user id into this palette").
 */

const NICK_TEXT = [
    'text-green',
    'text-user-violet',
    'text-user-blue',
    'text-user-clay',
] as const;

const AVATAR_BG = [
    'bg-green',
    'bg-user-violet',
    'bg-user-blue',
    'bg-user-clay',
] as const;

function hash(value: string): number {
    let h = 0;

    for (let i = 0; i < value.length; i++) {
        h = (h << 5) - h + value.charCodeAt(i);
        h |= 0;
    }

    return Math.abs(h);
}

/**
 * A lowercase, single-word handle from a display name (e.g. "Nuno Maduro" → "nuno").
 */
export function handleFor(name: string): string {
    return name.trim().split(/\s+/)[0]?.toLowerCase() ?? '';
}

/**
 * Up to two uppercase initials from a display name (e.g. "Nuno Maduro" → "NM").
 */
export function initialsFor(name: string): string {
    return name
        .trim()
        .split(/\s+/)
        .slice(0, 2)
        .map((part) => part[0] ?? '')
        .join('')
        .toUpperCase();
}

/**
 * The text color token for a user's handle/nick, hashed from their id.
 */
export function nickColorFor(id: string | number): string {
    return NICK_TEXT[hash(String(id)) % NICK_TEXT.length];
}

/**
 * The background color token for a user's avatar square, hashed from their id.
 */
export function avatarColorFor(id: string | number): string {
    return AVATAR_BG[hash(String(id)) % AVATAR_BG.length];
}
