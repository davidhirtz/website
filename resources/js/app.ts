import {config, subscribe} from './app/consent';
import gtag from "./app/consent/analytics/gtag.ts";
import observe from "./app/observer.ts";
import render from "./app/router.ts";

import "../css/app.css";
import nav from "./app/nav.ts";

export default (cookieDomain: string, tagId: string) => {
    if (cookieDomain) {
        config({domain: cookieDomain});
    }

    if (tagId) {
        subscribe((categories) => {
            if (categories.has('analytics')) {
                gtag(tagId);
            }
        });
    }

    render((_$container) => {
        nav();
        observe();
    });
};