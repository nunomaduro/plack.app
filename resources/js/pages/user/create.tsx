import { Form, Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { memberLabel } from '@/lib/utils';
import { login } from '@/routes';
import { store } from '@/routes/register';

type WorkspaceInvitation = {
    code: string;
    workspace: string;
    memberCount: number;
};

type WorkspaceJoin = {
    code: string;
    workspace: {
        id: string;
        name: string;
        memberCount: number;
    };
};

type Props = {
    workspaceInvitation?: WorkspaceInvitation | null;
    workspaceJoin?: WorkspaceJoin | null;
};

const fieldWrap =
    'flex h-[46px] items-center gap-[9px] border border-line bg-ink-950 px-[14px] transition-colors focus-within:border-amber';
const inputClass =
    'min-w-0 flex-1 bg-transparent text-[13.5px] text-fg caret-green outline-none placeholder:text-faint';
const labelClass = 'mb-2 text-[9px] uppercase tracking-[.22em] text-mute';

export default function Register({
    workspaceInvitation,
    workspaceJoin,
}: Props) {
    const [showPw, setShowPw] = useState(false);

    const authQuery = workspaceInvitation
        ? { invitation: workspaceInvitation.code }
        : workspaceJoin
          ? { join: workspaceJoin.code }
          : null;
    const loginHref = authQuery ? login.url({ query: authQuery }) : login();

    return (
        <div className="relative min-h-screen overflow-hidden bg-ink-950 font-mono text-fg">
            <Head title="Register" />

            <div
                className="pointer-events-none absolute inset-0"
                style={{
                    background:
                        'radial-gradient(58% 46% at 50% 44%, rgba(229,162,61,.06), transparent 72%)',
                }}
            />

            <div className="absolute top-6 right-9 z-10 text-xs tracking-[.02em] text-[#5a5344]">
                already have one?{' '}
                <Link
                    href={loginHref}
                    className="border-b border-line text-dim transition-colors hover:text-amber"
                >
                    log in →
                </Link>
            </div>

            <div className="absolute inset-0 flex flex-col items-center justify-center p-10">
                <div className="mb-[30px] text-center">
                    <div className="inline-flex items-center text-[27px] font-semibold tracking-[.01em] text-amber">
                        plack
                        <span className="ml-[7px] inline-block h-[22px] w-2 animate-blink bg-green" />
                    </div>
                    <div className="mt-5 text-[9px] tracking-[.32em] text-mute uppercase">
                        create account
                    </div>
                    <div className="mt-[9px] text-[13px] tracking-[.01em] text-dim">
                        Somewhere quiet for your team to actually talk.
                    </div>
                </div>

                {workspaceInvitation && (
                    <div className="mb-5 w-[340px] border border-line bg-ink-900 px-[14px] py-3 text-[12px] text-dim">
                        <span className="text-green">→</span> you've been
                        invited to{' '}
                        <span className="font-semibold text-amber">
                            {workspaceInvitation.workspace}
                        </span>{' '}
                        <span className="text-mute">
                            ({memberLabel(workspaceInvitation.memberCount)})
                        </span>
                        . Create your account to accept.
                    </div>
                )}

                {workspaceJoin && !workspaceInvitation && (
                    <div className="mb-5 w-[340px] border border-line bg-ink-900 px-[14px] py-3 text-[12px] text-dim">
                        <span className="text-green">→</span> you're joining{' '}
                        <span className="font-semibold text-amber">
                            {workspaceJoin.workspace.name}
                        </span>{' '}
                        <span className="text-mute">
                            ({memberLabel(workspaceJoin.workspace.memberCount)})
                        </span>
                        . Create your account to continue.
                    </div>
                )}

                <Form
                    {...store.form()}
                    resetOnSuccess={['password', 'password_confirmation']}
                    disableWhileProcessing
                    className="flex w-[340px] flex-col gap-[15px]"
                >
                    {({ processing, errors }) => (
                        <>
                            <div>
                                <div className={labelClass}>your name</div>
                                <div className={fieldWrap}>
                                    <span className="text-[13px] text-green">
                                        &gt;
                                    </span>
                                    <input
                                        type="text"
                                        name="name"
                                        required
                                        autoFocus
                                        autoComplete="name"
                                        placeholder="Nuno Maduro"
                                        className={inputClass}
                                    />
                                </div>
                                <InputError
                                    message={errors.name}
                                    className="mt-1.5"
                                />
                            </div>

                            <div>
                                <div className={labelClass}>work email</div>
                                <div className={fieldWrap}>
                                    <span className="text-[13px] text-green">
                                        &gt;
                                    </span>
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        autoComplete="email"
                                        placeholder="you@company.com"
                                        className={inputClass}
                                    />
                                </div>
                                <InputError
                                    message={errors.email}
                                    className="mt-1.5"
                                />
                            </div>

                            <div>
                                <div className={labelClass}>password</div>
                                <div className={fieldWrap}>
                                    <span className="text-[13px] text-green">
                                        &gt;
                                    </span>
                                    <input
                                        type={showPw ? 'text' : 'password'}
                                        name="password"
                                        required
                                        autoComplete="new-password"
                                        placeholder="at least 8 characters"
                                        className={inputClass}
                                    />
                                    <button
                                        type="button"
                                        tabIndex={-1}
                                        onClick={() => setShowPw((v) => !v)}
                                        className="text-[11px] tracking-[.06em] text-mute transition-colors hover:text-amber"
                                    >
                                        {showPw ? 'hide' : 'show'}
                                    </button>
                                </div>
                                <InputError
                                    message={errors.password}
                                    className="mt-1.5"
                                />
                            </div>

                            <div>
                                <div className={labelClass}>
                                    confirm password
                                </div>
                                <div className={fieldWrap}>
                                    <span className="text-[13px] text-green">
                                        &gt;
                                    </span>
                                    <input
                                        type={showPw ? 'text' : 'password'}
                                        name="password_confirmation"
                                        required
                                        autoComplete="new-password"
                                        placeholder="repeat your password"
                                        className={inputClass}
                                    />
                                </div>
                                <InputError
                                    message={errors.password_confirmation}
                                    className="mt-1.5"
                                />
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                data-test="register-user-button"
                                className="mt-1.5 flex h-12 w-full items-center justify-center gap-2 border border-amber bg-amber text-[13.5px] font-semibold tracking-[.03em] text-ink-950 transition-colors hover:bg-[#f0b452] disabled:opacity-60"
                            >
                                create account →
                            </button>

                            <div className="mt-1 text-center text-[11px] leading-relaxed text-faint">
                                By continuing you agree to the{' '}
                                <a
                                    href="/terms"
                                    className="border-b border-line text-mute hover:text-dim"
                                >
                                    terms
                                </a>{' '}
                                &amp;{' '}
                                <a
                                    href="/privacy"
                                    className="border-b border-line text-mute hover:text-dim"
                                >
                                    privacy policy
                                </a>
                                .
                            </div>
                        </>
                    )}
                </Form>
            </div>
        </div>
    );
}
