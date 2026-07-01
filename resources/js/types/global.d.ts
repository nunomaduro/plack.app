import type { Auth } from '@/types/auth';
import type { FlashToast } from '@/types/ui';
import type { CurrentWorkspace, WorkspaceSummary } from '@/types/workspace';

declare module '@inertiajs/core' {
    export interface InertiaConfig {
        flashDataType: {
            toast?: FlashToast;
        };
        sharedPageProps: {
            name: string;
            auth: Auth;
            sidebarOpen: boolean;
            navWorkspaces: WorkspaceSummary[];
            currentWorkspace: CurrentWorkspace | null;
            [key: string]: unknown;
        };
    }
}
