import { Link } from '@inertiajs/react';
import { home } from '@/routes';
import type { AuthLayoutProps } from '@/types';

export default function AuthSimpleLayout({
    children,
    title,
    description,
}: AuthLayoutProps) {
    return (
        <div className="relative min-h-screen overflow-hidden bg-ink-950 font-mono text-fg">
            {/* faint warm center glow */}
            <div
                className="pointer-events-none absolute inset-0"
                style={{
                    background:
                        'radial-gradient(58% 46% at 50% 44%, rgba(229,162,61,.06), transparent 72%)',
                }}
            />

            <div className="absolute inset-0 flex flex-col items-center justify-center px-6">
                <div className="mb-[34px] text-center">
                    <Link
                        href={home()}
                        className="inline-flex items-center text-[27px] font-semibold tracking-[.01em] text-amber"
                    >
                        plack
                        <span className="ml-[7px] inline-block h-[22px] w-2 animate-blink bg-green" />
                    </Link>
                    <div className="mt-5 text-[9px] tracking-[.32em] text-mute uppercase">
                        {title}
                    </div>
                    {description && (
                        <div className="mt-[9px] max-w-[340px] text-[13px] tracking-[.01em] text-dim">
                            {description}
                        </div>
                    )}
                </div>

                <div className="w-full max-w-[340px]">{children}</div>
            </div>
        </div>
    );
}
