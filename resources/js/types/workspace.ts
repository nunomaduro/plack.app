export type Channel = {
    id: string;
    name: string;
    slug: string;
};

export type WorkspaceSummary = {
    id: string;
    name: string;
    slug: string;
};

export type CurrentWorkspace = WorkspaceSummary & {
    channels: Channel[];
};
