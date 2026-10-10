export type ConsentConfig = {
    name: string;
    domain?: string | null;
    expires?: string;
    version: string;
    logUrl?: string | null;
    // First-party cookie names per category (`*` matches anything), deleted when the category is withdrawn
    cookies: Record<string, string[]>;
    // Every cookie the banner lists, warned about otherwise when `debug` is set
    declared: string[];
    debug?: boolean;
}

export let config: ConsentConfig = {
    name: '_cc',
    version: '1',
    cookies: {},
    declared: [],
};

export const configure = (options: Partial<ConsentConfig>) => {
    config = {...config, ...options};
}
