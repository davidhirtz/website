import {Categories} from "../categories.ts";

declare global {
    interface Window {
        dataLayer: (IArguments | Record<string, unknown>)[];
        gtag: Gtag.Gtag;
    }
}

let isLoaded = false;

/**
 * Consent Mode v2 in its basic form: nothing of Google's loads before the visitor allowed analytics, and the
 * signals tell the tags (and Google Ads, which requires them) what was allowed. `marketing` is not a category of
 * the template; a project adding one gets the ad signals for free.
 */
const getConsentState = (categories: Categories): Gtag.ConsentParams => {
    const state = (category: string): "granted" | "denied" => categories.has(category) ? 'granted' : 'denied';

    return {
        analytics_storage: state('analytics'),
        ad_storage: state('marketing'),
        ad_user_data: state('marketing'),
        ad_personalization: state('marketing'),
    };
}

const loadScript = (src: string) => {
    const $tag = document.createElement('script');
    $tag.async = true;
    $tag.src = src;
    document.head.appendChild($tag);
}

/**
 * Loads Google Tag Manager for a `GTM-` ID, the Google tag (Analytics) otherwise. A Tag Manager container sees the
 * choice as the `cc_update` event and its `cc_categories` variable.
 */
export default (tagId: string, categories: Categories): void => {
    const consentState = getConsentState(categories);

    window.dataLayer = window.dataLayer || [];
    window.gtag = window.gtag || function () {
        window.dataLayer.push(arguments);
    }

    if (isLoaded) {
        window.gtag('consent', 'update', consentState);
    } else {
        window.gtag('consent', 'default', consentState);

        if (tagId.startsWith('GTM-')) {
            window.dataLayer.push({'gtm.start': Date.now(), event: 'gtm.js'});
            loadScript(`https://www.googletagmanager.com/gtm.js?id=${tagId}`);
        } else {
            window.gtag('js', new Date());
            window.gtag('config', tagId);
            loadScript(`https://www.googletagmanager.com/gtag/js?id=${tagId}`);
        }

        isLoaded = true;
    }

    window.dataLayer.push({event: 'cc_update', cc_categories: [...categories]});
}
