import { Form, Head } from '@inertiajs/react';
import RegenerateWorkspaceJoinLinkController from '@/actions/App/Http/Controllers/RegenerateWorkspaceJoinLinkController';
import CancelInvitationDialog from '@/components/cancel-invitation-dialog';
import DeleteWorkspaceDialog from '@/components/delete-workspace-dialog';
import InputError from '@/components/input-error';
import InviteMemberDialog from '@/components/invite-member-dialog';
import RemoveMemberDialog from '@/components/remove-member-dialog';
import { useClipboard } from '@/hooks/use-clipboard';
import WorkspaceLayout, {
    MobileSidebarToggle,
} from '@/layouts/workspace-layout';
import { update } from '@/routes/workspace';

type Channel = {
    id: string;
    name: string;
    slug: string;
    unread_count: number;
    muted: boolean;
};

type Workspace = {
    id: string;
    name: string;
    slug: string;
    type: 'private' | 'public';
    channels: Channel[];
};

type WorkspaceSummary = {
    id: string;
    name: string;
    slug: string;
};

type Person = {
    id: string;
    name: string;
    email: string;
};

type Invitation = {
    code: string;
    email: string;
};

const fieldWrap =
    'flex h-[46px] items-center gap-[9px] border border-line bg-ink-950 px-[14px] transition-colors focus-within:border-amber';
const inputClass =
    'min-w-0 flex-1 bg-transparent text-[13.5px] text-fg caret-green outline-none placeholder:text-faint';
const labelClass = 'mb-2 text-[9px] uppercase tracking-[.22em] text-mute';
const sectionLabel =
    'mb-[10px] text-[9px] tracking-[.22em] text-mute uppercase';

export default function WorkspaceSettings({
    workspace,
    owner,
    members,
    invitations,
    publicJoinUrl,
    workspaces,
}: {
    workspace: Workspace;
    owner: Person;
    members: Person[];
    invitations: Invitation[];
    publicJoinUrl: string | null;
    workspaces?: WorkspaceSummary[];
}) {
    const [copiedPublicJoinUrl, copyPublicJoinUrl] = useClipboard();
    const isPublic = workspace.type === 'public';

    return (
        <WorkspaceLayout
            workspace={workspace}
            workspaces={workspaces}
            canManage
        >
            <Head title={`${workspace.name} — settings`} />

            {/* header */}
            <header className="flex items-center justify-between gap-3 border-b border-line px-4 py-[15px] md:px-6">
                <div className="flex min-w-0 items-center gap-3">
                    <MobileSidebarToggle />
                    <div className="flex min-w-0 items-baseline gap-3">
                        <span className="text-[15px] font-semibold text-amber">
                            settings
                        </span>
                        <span className="truncate text-[11px] text-mute">
                            {workspace.name}
                        </span>
                    </div>
                </div>
            </header>

            <div className="flex-1 overflow-y-auto px-4 py-6 md:px-6 md:py-7">
                <div className="mx-auto flex max-w-[560px] flex-col gap-10">
                    {/* workspace details */}
                    <section>
                        <div className={sectionLabel}>workspace details</div>

                        <Form
                            {...update.form(workspace.slug)}
                            options={{ preserveScroll: true }}
                            className="flex flex-col gap-4"
                        >
                            {({ processing, errors }) => (
                                <>
                                    <div>
                                        <div className={labelClass}>name</div>
                                        <div className={fieldWrap}>
                                            <span className="text-[13px] text-green">
                                                &gt;
                                            </span>
                                            <input
                                                name="name"
                                                defaultValue={workspace.name}
                                                autoComplete="off"
                                                className={inputClass}
                                            />
                                        </div>
                                        <InputError
                                            message={errors.name}
                                            className="mt-1.5"
                                        />
                                    </div>

                                    <div>
                                        <div className={labelClass}>slug</div>
                                        <div className={fieldWrap}>
                                            <span className="text-[13px] text-green">
                                                &gt;
                                            </span>
                                            <input
                                                name="slug"
                                                defaultValue={workspace.slug}
                                                autoComplete="off"
                                                className={inputClass}
                                            />
                                        </div>
                                        <InputError
                                            message={errors.slug}
                                            className="mt-1.5"
                                        />
                                    </div>

                                    <button
                                        type="submit"
                                        disabled={processing}
                                        data-test="update-workspace-submit"
                                        className="flex h-11 items-center justify-center gap-2 self-start border border-amber px-6 text-[13px] font-medium tracking-[.04em] text-amber transition-colors hover:bg-amber hover:text-ink-950 disabled:opacity-60"
                                    >
                                        save
                                    </button>
                                </>
                            )}
                        </Form>
                    </section>

                    {isPublic && publicJoinUrl && (
                        <section>
                            <div className={sectionLabel}>public join link</div>

                            <div className="flex flex-col gap-3 border border-line bg-ink-950 px-[14px] py-4">
                                <p className="text-[12px] leading-relaxed text-mute">
                                    Anyone with this link can request to join
                                    this workspace. Regenerating it invalidates
                                    the previous link.
                                </p>

                                <div className="flex h-[46px] items-center gap-[9px] border border-line bg-ink-900 px-[14px]">
                                    <span className="text-[13px] text-green">
                                        &gt;
                                    </span>
                                    <input
                                        readOnly
                                        value={publicJoinUrl}
                                        data-test="public-join-link"
                                        className={inputClass}
                                    />
                                </div>

                                <div className="flex flex-wrap gap-2">
                                    <button
                                        type="button"
                                        data-test="copy-public-join-link"
                                        onClick={() =>
                                            copyPublicJoinUrl(publicJoinUrl)
                                        }
                                        className="flex h-10 items-center justify-center border border-line px-4 text-[12px] text-dim transition-colors hover:border-amber hover:text-amber"
                                    >
                                        {copiedPublicJoinUrl === publicJoinUrl
                                            ? 'copied'
                                            : 'copy link'}
                                    </button>

                                    <Form
                                        {...RegenerateWorkspaceJoinLinkController.form(
                                            workspace.slug,
                                        )}
                                        options={{ preserveScroll: true }}
                                    >
                                        {({ processing }) => (
                                            <button
                                                type="submit"
                                                disabled={processing}
                                                data-test="regenerate-public-join-link"
                                                className="flex h-10 items-center justify-center border border-amber px-4 text-[12px] text-amber transition-colors hover:bg-amber hover:text-ink-950 disabled:opacity-60"
                                            >
                                                regenerate link
                                            </button>
                                        )}
                                    </Form>
                                </div>
                            </div>
                        </section>
                    )}

                    {/* members */}
                    <section>
                        <div className="mb-[10px] flex items-center justify-between">
                            <span className={sectionLabel + ' mb-0'}>
                                members
                            </span>
                            {!isPublic && (
                                <InviteMemberDialog
                                    workspaceSlug={workspace.slug}
                                />
                            )}
                        </div>

                        <ul className="flex flex-col gap-[2px]">
                            <li className="flex items-center justify-between border border-line bg-ink-950 px-[14px] py-3">
                                <div className="flex min-w-0 flex-col">
                                    <span className="truncate text-[13px] text-fg">
                                        {owner.name}
                                    </span>
                                    <span className="truncate text-[11px] text-mute">
                                        {owner.email}
                                    </span>
                                </div>
                                <span className="text-[9px] tracking-[.22em] text-amber uppercase">
                                    owner
                                </span>
                            </li>

                            {members.map((member) => (
                                <li
                                    key={member.id}
                                    className="flex items-center justify-between border border-line bg-ink-950 px-[14px] py-3"
                                >
                                    <div className="flex min-w-0 flex-col">
                                        <span className="truncate text-[13px] text-fg">
                                            {member.name}
                                        </span>
                                        <span className="truncate text-[11px] text-mute">
                                            {member.email}
                                        </span>
                                    </div>

                                    <RemoveMemberDialog
                                        workspaceSlug={workspace.slug}
                                        member={member}
                                    />
                                </li>
                            ))}
                        </ul>
                    </section>

                    {/* pending invitations */}
                    {!isPublic && invitations.length > 0 && (
                        <section>
                            <div className={sectionLabel}>
                                pending invitations
                            </div>

                            <ul className="flex flex-col gap-[2px]">
                                {invitations.map((invitation) => (
                                    <li
                                        key={invitation.code}
                                        className="flex items-center justify-between border border-line bg-ink-950 px-[14px] py-3"
                                    >
                                        <span className="truncate text-[12.5px] text-dim">
                                            {invitation.email}
                                        </span>

                                        <CancelInvitationDialog
                                            workspaceSlug={workspace.slug}
                                            invitation={invitation}
                                        />
                                    </li>
                                ))}
                            </ul>
                        </section>
                    )}

                    {/* danger zone */}
                    <section>
                        <div className={sectionLabel}>danger zone</div>

                        <div className="flex flex-col gap-4 border border-destructive/40 bg-destructive/5 px-[14px] py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <p className="text-[13px] text-fg">
                                    Delete workspace
                                </p>
                                <p className="mt-1 text-[11px] text-mute">
                                    Permanently delete this workspace and all of
                                    its data.
                                </p>
                            </div>

                            <DeleteWorkspaceDialog workspace={workspace} />
                        </div>
                    </section>
                </div>
            </div>
        </WorkspaceLayout>
    );
}
