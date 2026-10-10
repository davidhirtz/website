import {config} from "./config.ts";

const separator = '|';

const getCookieNames = (): string[] => document.cookie
    ? document.cookie.split('; ').map((cookie) => cookie.split('=')[0])
    : [];

const getCookie = (name: string): string | null => {
    const prefix = name + '=';
    const cookie = document.cookie.split('; ').find((cookie) => cookie.startsWith(prefix));

    return cookie ? decodeURIComponent(cookie.slice(prefix.length)) : null;
}

const setCookie = (value: string, expires: string) => {
    document.cookie = `${config.name}=${encodeURIComponent(value)}; expires=${expires}`
        + (config.domain ? `; domain=${config.domain}` : '')
        + '; path=/; samesite=lax'
        + (location.protocol === 'https:' ? '; secure' : '');
}

export const matchesCookie = (name: string, pattern: string): boolean => {
    const regex = pattern.split('*').map((part) => part.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')).join('.*');
    return new RegExp(`^${regex}$`).test(name);
}

// Google Analytics writes to the topmost domain it can, so every parent domain is tried
export const deleteCookies = (patterns: string[]) => {
    const parts = location.hostname.split('.');
    const domains = [''];

    for (let i = 0; i < parts.length - 1; i++) {
        domains.push('; domain=.' + parts.slice(i).join('.'));
    }

    getCookieNames()
        .filter((name) => patterns.some((pattern) => matchesCookie(name, pattern)))
        .forEach((name) => domains.forEach((domain) => {
            document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/${domain}`;
        }));
}

export const readConsentCookie = (): { id: string, categories: string[] } | null => {
    const [version, id, categories] = (getCookie(config.name) || '').split(separator);

    return version === config.version && id
        ? {id, categories: categories ? categories.split(',') : []}
        : null;
}

export const writeConsentCookie = (id: string, categories: Set<string>) => {
    const getDefaultExpires = () => {
        const date = new Date();
        date.setFullYear(date.getFullYear() + 1);
        return date.toUTCString();
    }

    setCookie([config.version, id, [...categories].join(',')].join(separator), config.expires || getDefaultExpires());
}

export const warnUndeclaredCookies = () => {
    const undeclared = getCookieNames()
        .filter((name) => !config.declared.some((pattern) => matchesCookie(name, pattern)));

    if (undeclared.length) {
        console.warn('Cookies missing from App\\Consent\\Consent:', undeclared.join(', '));
    }
}
