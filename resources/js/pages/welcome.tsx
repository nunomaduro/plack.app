import { Head, Link, usePage } from '@inertiajs/react';
import { login, register } from '@/routes';
import { index as workspaces } from '@/routes/workspace';

export default function Welcome() {
    const { auth } = usePage().props;

    return (
        <div className="relative min-h-screen overflow-hidden bg-ink-950 font-mono text-fg">
            <Head title="plack — team chat" />

            {/* faint warm center glow */}
            <div
                className="pointer-events-none absolute inset-0"
                style={{
                    background:
                        'radial-gradient(58% 46% at 50% 44%, rgba(229,162,61,.06), transparent 72%)',
                }}
            />

            {/* top corners — minimal chrome */}
            {auth.user ? (
                <Link
                    href={workspaces()}
                    className="absolute top-6 right-9 z-10 text-xs tracking-[.02em] text-[#5a5344] transition-colors hover:text-fg"
                >
                    workspaces
                </Link>
            ) : (
                <Link
                    href={login()}
                    className="absolute top-6 right-9 z-10 text-xs tracking-[.02em] text-[#5a5344] transition-colors hover:text-fg"
                    data-test="login-link"
                >
                    log in
                </Link>
            )}

            {/* centered lockup */}
            <div className="absolute inset-0 flex flex-col items-center justify-center px-10 text-center">
                <div className="flex items-center text-[44px] font-semibold tracking-[.01em] text-amber">
                    plack
                    <span className="ml-2 inline-block h-[34px] w-[11px] animate-blink bg-green" />
                </div>

                <p className="mt-6 text-[14.5px] tracking-[.02em] text-dim">
                    Somewhere quiet for your team to actually talk.
                </p>

                <Link
                    href={auth.user ? workspaces() : register()}
                    className="mt-11 border-b border-[#4a3f28] pb-[5px] text-[13.5px] tracking-[.03em] text-amber transition-colors hover:border-amber"
                    data-test="register-link"
                >
                    {auth.user
                        ? 'open your workspace →'
                        : 'create your workspace →'}
                </Link>
            </div>
        </div>
    );
}
