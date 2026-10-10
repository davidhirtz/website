import {config} from "./config.ts";

export const createConsentId = (): string => {
    if (crypto.randomUUID) {
        return crypto.randomUUID();
    }

    // `randomUUID()` needs a secure context
    const bytes = crypto.getRandomValues(new Uint8Array(16));
    bytes[6] = (bytes[6] & 0x0f) | 0x40;
    bytes[8] = (bytes[8] & 0x3f) | 0x80;

    const hex = [...bytes].map((byte) => byte.toString(16).padStart(2, '0')).join('');
    return `${hex.slice(0, 8)}-${hex.slice(8, 12)}-${hex.slice(12, 16)}-${hex.slice(16, 20)}-${hex.slice(20)}`;
}

// `keepalive` outlives the reload a withdrawal triggers
export const logConsent = (id: string, categories: Set<string>) => {
    if (!config.logUrl) {
        return;
    }

    fetch(config.logUrl, {
        method: 'POST',
        body: new URLSearchParams({
            id,
            version: config.version,
            categories: [...categories].join(','),
        }),
        keepalive: true,
    }).catch(() => null);
}
