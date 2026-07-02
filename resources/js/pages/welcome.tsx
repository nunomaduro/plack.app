import { Form, Head, Link, usePage } from '@inertiajs/react';
import InputError from '@/components/input-error';
import { store as earlyAccessStore } from '@/routes/early-access';
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
            {auth.user && (
                <Link
                    href={workspaces()}
                    className="absolute top-6 right-9 z-10 text-xs tracking-[.02em] text-[#5a5344] transition-colors hover:text-fg"
                >
                    workspaces
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

                {auth.user ? (
                    <Link
                        href={workspaces()}
                        className="mt-11 border-b border-[#4a3f28] pb-[5px] text-[13.5px] tracking-[.03em] text-amber transition-colors hover:border-amber"
                        data-test="workspace-link"
                    >
                        open your workspace →
                    </Link>
                ) : (
                    <Form
                        {...earlyAccessStore.form()}
                        resetOnSuccess={['email']}
                        className="mt-11 flex w-full max-w-[360px] flex-col items-center"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="flex h-[46px] w-full items-center gap-[9px] border border-line bg-ink-950 px-[14px] transition-colors focus-within:border-amber">
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        autoComplete="email"
                                        placeholder="you@team.com"
                                        className="min-w-0 flex-1 bg-transparent text-[13.5px] text-fg caret-green outline-none placeholder:text-faint"
                                        data-test="early-access-email"
                                    />
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="shrink-0 text-[13.5px] tracking-[.03em] text-amber transition-colors hover:text-fg disabled:opacity-50"
                                        data-test="early-access-submit"
                                    >
                                        request access →
                                    </button>
                                </div>
                                <InputError
                                    message={errors.email}
                                    className="mt-2 self-start"
                                />
                            </>
                        )}
                    </Form>
                )}
            </div>
        </div>
    );
}
