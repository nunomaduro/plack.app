import { Form, Head } from '@inertiajs/react';
import { Mail } from 'lucide-react';
import WorkspaceController from '@/actions/App/Http/Controllers/WorkspaceController';
import CancelInvitationDialog from '@/components/cancel-invitation-dialog';
import DeleteWorkspaceDialog from '@/components/delete-workspace-dialog';
import Heading from '@/components/heading';
import InputError from '@/components/input-error';
import InviteMemberDialog from '@/components/invite-member-dialog';
import RemoveMemberDialog from '@/components/remove-member-dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AppLayout from '@/layouts/app-layout';
import { index, show } from '@/routes/workspace';
import type { BreadcrumbItem } from '@/types';

type Workspace = {
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

export default function WorkspaceSettings({
    workspace,
    owner,
    members,
    invitations,
}: {
    workspace: Workspace;
    owner: Person;
    members: Person[];
    invitations: Invitation[];
}) {
    const breadcrumbs: BreadcrumbItem[] = [
        {
            title: 'Workspaces',
            href: index(),
        },
        {
            title: workspace.name,
            href: show(workspace.slug),
        },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={workspace.name} />

            <div className="flex h-full flex-1 flex-col gap-8 overflow-x-auto rounded-xl p-4">
                <section className="flex flex-col gap-4">
                    <Heading
                        variant="small"
                        title="Workspace details"
                        description="Update the name and slug of your workspace."
                    />

                    <Form
                        {...WorkspaceController.update.form(workspace.slug)}
                        options={{ preserveScroll: true }}
                        className="flex max-w-md flex-col gap-4"
                    >
                        {({ processing, errors }) => (
                            <>
                                <div className="grid gap-2">
                                    <Label htmlFor="name">Name</Label>

                                    <Input
                                        id="name"
                                        name="name"
                                        defaultValue={workspace.name}
                                        autoComplete="off"
                                    />

                                    <InputError message={errors.name} />
                                </div>

                                <div className="grid gap-2">
                                    <Label htmlFor="slug">Slug</Label>

                                    <Input
                                        id="slug"
                                        name="slug"
                                        defaultValue={workspace.slug}
                                        autoComplete="off"
                                    />

                                    <InputError message={errors.slug} />
                                </div>

                                <Button
                                    type="submit"
                                    className="self-start"
                                    disabled={processing}
                                    data-test="update-workspace-submit"
                                >
                                    Save
                                </Button>
                            </>
                        )}
                    </Form>
                </section>

                <section className="flex flex-col gap-4">
                    <div className="flex items-center justify-between">
                        <Heading
                            variant="small"
                            title="Members"
                            description="People with access to this workspace."
                        />

                        <InviteMemberDialog workspaceSlug={workspace.slug} />
                    </div>

                    <ul className="flex flex-col gap-2">
                        <li className="flex items-center justify-between rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border">
                            <div className="flex flex-col">
                                <span className="font-medium">
                                    {owner.name}
                                </span>
                                <span className="text-sm text-muted-foreground">
                                    {owner.email}
                                </span>
                            </div>

                            <span className="text-sm text-muted-foreground">
                                Owner
                            </span>
                        </li>

                        {members.map((member) => (
                            <li
                                key={member.id}
                                className="flex items-center justify-between rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                            >
                                <div className="flex flex-col">
                                    <span className="font-medium">
                                        {member.name}
                                    </span>
                                    <span className="text-sm text-muted-foreground">
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

                {invitations.length > 0 && (
                    <section className="flex flex-col gap-4">
                        <Heading
                            variant="small"
                            title="Pending invitations"
                            description="Invitations that haven't been accepted yet."
                        />

                        <ul className="flex flex-col gap-2">
                            {invitations.map((invitation) => (
                                <li
                                    key={invitation.code}
                                    className="flex items-center justify-between rounded-xl border border-sidebar-border/70 p-4 dark:border-sidebar-border"
                                >
                                    <div className="flex items-center gap-4">
                                        <div className="flex h-10 w-10 items-center justify-center rounded-full bg-muted">
                                            <Mail className="h-5 w-5 text-muted-foreground" />
                                        </div>

                                        <span className="text-sm">
                                            {invitation.email}
                                        </span>
                                    </div>

                                    <CancelInvitationDialog
                                        workspaceSlug={workspace.slug}
                                        invitation={invitation}
                                    />
                                </li>
                            ))}
                        </ul>
                    </section>
                )}

                <section className="flex flex-col gap-4">
                    <div className="flex items-center justify-between rounded-xl border border-red-100 bg-red-50 p-4 dark:border-red-200/10 dark:bg-red-700/10">
                        <div className="space-y-0.5 text-red-600 dark:text-red-100">
                            <p className="font-medium">Delete workspace</p>
                            <p className="text-sm">
                                Permanently delete this workspace and all of its
                                data.
                            </p>
                        </div>

                        <DeleteWorkspaceDialog workspace={workspace} />
                    </div>
                </section>
            </div>
        </AppLayout>
    );
}
