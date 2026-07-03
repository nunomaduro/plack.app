import type {
    PendingInvitation,
    PendingWorkspaceJoin,
} from '@/components/pending-invitations';
import type { Auth } from '@/types/auth';
import type { ChannelVisibilityOption } from '@/types/channel';
import type { FlashToast } from '@/types/ui';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        flashDataType: {
            toast?: FlashToast;
        };
        sharedPageProps: {
            name: string;
            auth: Auth;
            channelVisibilityOptions: ChannelVisibilityOption[];
            pendingInvitations: PendingInvitation[];
            pendingWorkspaceJoin: PendingWorkspaceJoin | null;
            sidebarOpen: boolean;
            [key: string]: unknown;
        };
    }
}
