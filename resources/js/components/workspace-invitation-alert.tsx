import { InfoIcon } from 'lucide-react';
import { Alert, AlertDescription } from '@/components/ui/alert';

type WorkspaceInvitation = {
    code: string;
    workspace: string;
};

export default function WorkspaceInvitationAlert({
    invitation,
    action,
}: {
    invitation: WorkspaceInvitation;
    action: string;
}) {
    return (
        <Alert className="border-blue-200 bg-blue-50 text-blue-900 dark:border-blue-900/50 dark:bg-blue-950/50 dark:text-blue-100 [&>svg]:text-blue-600 dark:[&>svg]:text-blue-400">
            <InfoIcon />
            <AlertDescription className="text-blue-900 dark:text-blue-100">
                {action} to join the “{invitation.workspace}” workspace.
            </AlertDescription>
        </Alert>
    );
}

export type { WorkspaceInvitation };
