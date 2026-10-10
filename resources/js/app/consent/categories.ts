import {emit} from "./subscribers.ts";
import {config} from "./config.ts";
import {deleteCookies, writeConsentCookie} from "./cookies.ts";
import {createConsentId, logConsent} from "./log.ts";

export type Categories = Set<string>;

export let categories: Categories = new Set();
export let consentId: string | null = null;

/**
 * Replaces the visitor's choice. A category taken back has its cookies deleted and reloads the page, as the
 * scripts it loaded cannot be unloaded.
 */
export const setCategories = (selected: string[], fromCookie: boolean = false, id: string | null = null) => {
    const previous = categories;

    categories = new Set(selected.filter((category) => category !== ''));
    consentId = id || consentId || createConsentId();

    if (!fromCookie) {
        writeConsentCookie(consentId, categories);
        logConsent(consentId, categories);

        const withdrawn = [...previous].filter((category) => !categories.has(category));

        if (withdrawn.length) {
            deleteCookies(withdrawn.flatMap((category) => config.cookies[category] || []));
            location.reload();
            return;
        }
    }

    emit(fromCookie);
}

export const addCategories = (added: string[]) => setCategories([...categories, ...added]);
