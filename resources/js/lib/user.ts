/**
 * Frontend derivations for the terminal UI: the backend User only carries
 * name/email/id, but the design shows lowercase handles, initials avatars, and
 * a per-user color. These derive that look deterministically so the same user
 * always renders identically.
 *
 * The color is an OKLCH value with fixed lightness and chroma, rotated around
 * the hue axis by a hash of the name. The four hand-picked handoff colors
 * (green/violet/blue/clay) all sit at L≈0.71, C≈0.115 and differ only in hue —
 * so pinning L/C to that band and varying hue yields infinite unique colors
 * that stay inside the palette's region and never clash with the dark surface.
 * OKLCH (not HSL) because it is perceptually uniform: fixed L/C looks equally
 * bright and saturated at every hue.
 */

const NICK_LIGHTNESS = 0.71;
const NICK_CHROMA = 0.115;

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
export function nickColorFor(name: string): string {
    const hue = hash(name) % 360;

    return `oklch(${NICK_LIGHTNESS} ${NICK_CHROMA} ${hue})`;
}
