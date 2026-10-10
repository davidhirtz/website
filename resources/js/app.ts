import google from "./app/consent/analytics/google.ts";
import nav from "./app/nav.ts";
import observe from "./app/observer.ts";
import render from "./app/router.ts";
import {ConsentConfig} from "./app/consent/config.ts";
import {init, subscribe} from './app/consent';

import "./app/components/ToggleButton.ts";

import "../css/app.css";
import embed from "./app/embed.ts";

type AppConfig = {
    cookieDomain?: string | null;
    tagId?: string | null;
    consent: Partial<ConsentConfig>;
}

export default ({cookieDomain, tagId, consent}: AppConfig) => {
    init({...consent, domain: cookieDomain});

    if (tagId) {
        subscribe((categories) => {
            if (categories.has('analytics')) {
                google(tagId, categories);
            }
        });
    }

    render(($container) => {
        nav();

        embed($container);
        observe($container);
    });
};
