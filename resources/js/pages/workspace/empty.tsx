import { Head, usePage } from '@inertiajs/react';
import CreateWorkspaceDialog from '@/components/create-workspace-dialog';
import PendingInvitations from '@/components/pending-invitations';

/**
 * Shown when the current user has no workspaces yet (e.g. right after
 * registration). The workspace index redirects here instead of listing
 * workspaces — Plack is workspace-scoped, so the one job is to create the
 * first one, after which the index redirects straight into it. Any pending
 * invitations are surfaced here so an invited user can join without one.
 */
export default function WorkspaceEmpty() {
    const { pendingInvitations, pendingWorkspaceJoin } = usePage().props;

    return (
        <div className="flex min-h-screen flex-col items-center justify-center gap-8 bg-ink-950 px-10 text-center font-mono">
            <Head title="Create your workspace" />

            {/* wordmark */}
            <div className="inline-flex items-center text-[27px] font-semibold text-amber">
                plack
                <span className="ml-[7px] inline-block h-[22px] w-[8px] animate-blink bg-green" />
            </div>

            <div>
                <div className="text-[9px] tracking-[.32em] text-mute uppercase">
                    no workspaces yet
                </div>
                <p className="mt-[9px] text-[13px] text-dim">
                    Create your first workspace to get started.
                </p>
            </div>

            <CreateWorkspaceDialog />

            {(pendingInvitations.length > 0 || pendingWorkspaceJoin) && (
                <div className="w-[320px] border border-line bg-ink-900 px-4 py-4 text-left">
                    <div className="mb-3 text-center text-[9px] tracking-[.22em] text-mute uppercase">
                        pending access
                    </div>

                    <PendingInvitations
                        invitations={pendingInvitations}
                        workspaceJoin={pendingWorkspaceJoin}
                    />
                </div>
            )}
        </div>
    );
}
