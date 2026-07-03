import { Form, Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import InputError from '@/components/input-error';
import { memberLabel } from '@/lib/utils';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

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
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
    workspaceInvitation?: WorkspaceInvitation | null;
    workspaceJoin?: WorkspaceJoin | null;
};

const fieldWrap =
    'flex h-[46px] items-center gap-[9px] border border-line bg-ink-950 px-[14px] transition-colors focus-within:border-amber';
const inputClass =
    'min-w-0 flex-1 bg-transparent text-[13.5px] text-fg caret-green outline-none placeholder:text-faint';
const labelClass = 'mb-2 text-[9px] uppercase tracking-[.22em] text-mute';

export default function Login({
    status,
    canResetPassword,
    canRegister,
    workspaceInvitation,
    workspaceJoin,
}: Props) {
    const [showPw, setShowPw] = useState(false);
    const [remember, setRemember] = useState(true);

    const authQuery = workspaceInvitation
        ? { invitation: workspaceInvitation.code }
        : workspaceJoin
          ? { join: workspaceJoin.code }
          : null;
    const registerHref = authQuery
        ? register.url({ query: authQuery })
        : register();

    return (
        <div className="relative min-h-screen overflow-hidden bg-ink-950 font-mono text-fg">
            <Head title="Log in" />

            <div
                className="pointer-events-none absolute inset-0"
                style={{
                    background:
                        'radial-gradient(58% 46% at 50% 44%, rgba(229,162,61,.06), transparent 72%)',
                }}
            />

            {canRegister && (
                <div className="absolute top-6 right-9 z-10 text-xs tracking-[.02em] text-[#5a5344]">
                    new here?{' '}
                    <Link
                        href={registerHref}
                        className="border-b border-line text-dim transition-colors hover:text-amber"
                    >
                        create a workspace →
                    </Link>
                </div>
            )}

            <div className="absolute inset-0 flex flex-col items-center justify-center px-10">
                <div className="mb-[34px] text-center">
                    <div className="inline-flex items-center text-[27px] font-semibold tracking-[.01em] text-amber">
                        plack
                        <span className="ml-[7px] inline-block h-[22px] w-2 animate-blink bg-green" />
                    </div>
                    <div className="mt-5 text-[9px] tracking-[.32em] text-mute uppercase">
                        log in
                    </div>
                    <div className="mt-[9px] text-[13px] tracking-[.01em] text-dim">
                        Welcome back. Pick up where you left off.
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
                        . Log in to accept.
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
                        . Log in to continue.
                    </div>
                )}

                {status && (
                    <div className="mb-4 text-[12px] text-green">{status}</div>
                )}

                <Form
                    {...store.form()}
                    resetOnSuccess={['password']}
                    className="flex w-[340px] flex-col gap-4"
                >
                    {({ processing, errors }) => (
                        <>
                            <div>
                                <div className={labelClass}>email</div>
                                <div className={fieldWrap}>
                                    <span className="text-[13px] text-green">
                                        &gt;
                                    </span>
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        autoFocus
                                        autoComplete="email"
                                        placeholder="you@company.com"
                                        className={inputClass}
                                        data-test="email-input"
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
                                        autoComplete="current-password"
                                        placeholder="••••••••••"
                                        className={inputClass}
                                        data-test="password-input"
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

                            <div className="mt-0.5 flex items-center justify-between text-[12px]">
                                <label className="flex cursor-pointer items-center gap-[9px] text-dim select-none">
                                    <span className="flex h-[15px] w-[15px] flex-none items-center justify-center border border-line bg-ink-950">
                                        {remember && (
                                            <span className="h-[7px] w-[7px] bg-green" />
                                        )}
                                    </span>
                                    <input
                                        type="checkbox"
                                        name="remember"
                                        checked={remember}
                                        onChange={(e) =>
                                            setRemember(e.target.checked)
                                        }
                                        className="sr-only"
                                    />
                                    remember me
                                </label>
                                {canResetPassword && (
                                    <Link
                                        href={request()}
                                        className="border-b border-line text-mute transition-colors hover:text-amber"
                                    >
                                        forgot password?
                                    </Link>
                                )}
                            </div>

                            <button
                                type="submit"
                                disabled={processing}
                                data-test="login-button"
                                className="mt-2 flex h-12 w-full items-center justify-center gap-2 border border-amber text-[13.5px] font-medium tracking-[.04em] text-amber transition-colors hover:bg-amber hover:text-ink-950 disabled:opacity-60"
                            >
                                log in →
                            </button>

                            {canRegister && (
                                <div className="mt-3 text-center text-[12.5px] text-mute">
                                    new to plack?{' '}
                                    <Link
                                        href={registerHref}
                                        className="border-b border-[#4a3f28] text-amber transition-colors hover:border-amber"
                                    >
                                        create a workspace →
                                    </Link>
                                </div>
                            )}
                        </>
                    )}
                </Form>
            </div>
        </div>
    );
}
