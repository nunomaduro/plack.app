/**
 * Very light markdown for message bodies: bold, italic, inline code, and bare
 * links. Nothing else — no `[text](url)` syntax, headings, lists, or block code.
 *
 * The parser returns React nodes rather than an HTML string, so it is XSS-safe
 * by construction: literal text lands in React text children (which React
 * escapes) and we never touch `dangerouslySetInnerHTML`. Links are only matched
 * when they start with `http://` or `https://`, so a hostile scheme such as
 * `javascript:` can never reach an `href`.
 *
 * Markers: `**bold**`, `*italic*` or `_italic_`, `` `code` ``, and bare URLs
 * (`https://…`) which auto-link. Inline code is literal (its contents are not
 * re-parsed); bold/italic recurse so a bold span can contain italics or a link.
 * Only paired markers format — a dangling `*` or an unterminated `**bold` falls
 * through as plain text, so a message never breaks.
 */
import type { ReactNode } from 'react';

export function renderMarkdown(text: string): ReactNode {
    // A fresh regex per call: it is stateful (`g`) and this function recurses,
    // so a shared instance would corrupt the outer scan's position.
    const pattern =
        /`([^`]+)`|\*\*([\s\S]+?)\*\*|\*([^*]+?)\*|_([^_]+?)_|(https?:\/\/[^\s<]+)/g;

    const nodes: ReactNode[] = [];
    let lastIndex = 0;
    let key = 0;
    let match: RegExpExecArray | null;

    while ((match = pattern.exec(text)) !== null) {
        if (match.index > lastIndex) {
            nodes.push(text.slice(lastIndex, match.index));
        }

        const [, code, bold, italicStar, italicUnderscore, url] = match;

        if (code !== undefined) {
            nodes.push(
                <code
                    key={key++}
                    className="rounded-sm bg-ink-800 px-1 text-green"
                >
                    {code}
                </code>,
            );
        } else if (bold !== undefined) {
            nodes.push(
                <strong key={key++} className="font-semibold text-fg-bright">
                    {renderMarkdown(bold)}
                </strong>,
            );
        } else if (url !== undefined) {
            // Trailing sentence punctuation (and an unbalanced closing paren) is
            // almost never part of the URL — leave it as plain text after the link.
            let href = url;
            let trailing = href.match(/[.,;:!?]+$/)?.[0] ?? '';
            href = href.slice(0, href.length - trailing.length);
            if (href.endsWith(')') && !href.includes('(')) {
                href = href.slice(0, -1);
                trailing = `)${trailing}`;
            }

            nodes.push(
                <a
                    key={key++}
                    href={href}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-amber underline underline-offset-2"
                >
                    {href}
                </a>,
            );

            if (trailing !== '') {
                nodes.push(trailing);
            }
        } else {
            const italic = italicStar ?? italicUnderscore ?? '';
            nodes.push(<em key={key++}>{renderMarkdown(italic)}</em>);
        }

        lastIndex = match.index + match[0].length;
    }

    if (lastIndex < text.length) {
        nodes.push(text.slice(lastIndex));
    }

    // Keep a plain string when there was no markup, so callers that expect text
    // (and React reconciliation) stay simple.
    if (nodes.length === 1 && typeof nodes[0] === 'string') {
        return nodes[0];
    }

    return nodes;
}
