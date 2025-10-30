import {setCategories} from "./categories.ts";

type CookieConfig = {
    domain?: string;
    name?: string;
    expires?: string;
}

let cookie: CookieConfig = {
    name: '_cc',
};

const version = 'v2';

export const removeCookie = () => setCookie('', 'Thu, 01 Jan 1970 00:00:01 GMT');

const setCookie = (value: string, expires: string) => {
    document.cookie = `${cookie.name}=${value}; expires=${expires}`
        + (cookie.domain ? `; domain=${cookie.domain}` : '')
        + '; path=/; sameSite=Lax';
}

export const config = (config: CookieConfig) => {
    cookie = {...cookie, ...config};
}

export const saveCategoriesToCookie = (categories: Set<string>) => {
    const getDefaultExpires = () => {
        const date = new Date();
        date.setFullYear(date.getFullYear() + 1);
        return date.toUTCString();
    }

    const expires: string = (cookie.expires || getDefaultExpires());
    const value = [version, ...categories].join(',');

    setCookie(value, expires);
}

const cookies = document.cookie
    ? document.cookie.split('; ')
    : [];

for (let i = 0; i < cookies.length; i++) {
    const params = cookies[i].split('=');

    if (params[0] === cookie.name) {
        const categories = params[1];

        if (categories.startsWith(version)) {
            setCategories(categories.slice(version.length + 1), true);
            break;
        }
    }
}