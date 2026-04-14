import gtag from "./app/consent/analytics/gtag.ts";
import nav from "./app/nav.ts";
import observe from "./app/observer.ts";
import render from "./app/router.ts";
import {config, subscribe} from './app/consent';

import "./app/components/ToggleButton.ts";

import "../css/app.css";
import embed from "./app/embed.ts";

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

    render(($container) => {
        embed($container);
        nav();
        observe();
    });
};