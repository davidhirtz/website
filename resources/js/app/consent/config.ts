export type ConsentConfig = {
    name: string;
    domain?: string | null;
    expires?: string;
    version: string;
    logUrl?: string | null;
    // `*` matches anything
    cookies: Record<string, string[]>;
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
