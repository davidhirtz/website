import {config, ConsentConfig, configure} from "./consent/config.ts";
import {deleteCookies, readConsentCookie, warnUndeclaredCookies} from "./consent/cookies.ts";
import {subscribe} from "./consent/subscribers.ts";
import {categories, setCategories} from "./consent/categories.ts";

import "./consent/components/ConsentButton.ts";
import "./consent/components/ConsentModal.ts";
import "./consent/components/ConsentSettings.ts";

export const init = (options: Partial<ConsentConfig>) => {
    configure(options);

    const consent = readConsentCookie();

    if (consent) {
        setCategories(consent.categories, true, consent.id);
    }

    // A script still running when consent was withdrawn may rewrite its cookies while the page unloads
    deleteCookies(Object.entries(config.cookies)
        .filter(([category]) => !categories.has(category))
        .flatMap(([, names]) => names));

    if (options.debug) {
        subscribe(() => setTimeout(warnUndeclaredCookies, 3000));
    }
}

export {subscribe, categories};
