import {removeCookie} from "../cookies.ts";
import {Categories} from "../categories.ts";
import {subscribe} from "../subscribers.ts";
import defineComponent from '../../shared/DefineCustomElement';

defineComponent('cc-reset', class extends HTMLElement {
    // noinspection JSUnusedGlobalSymbols
    connectedCallback() {
        this.setAttribute('role', 'button');

        this.onclick = () => {
            removeCookie();
            location.reload();
        }

        subscribe((categories: Categories) => {
            this.hidden = categories.size == 0;
        });
    }
});