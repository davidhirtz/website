import {Categories} from "../categories.ts";
import {subscribe} from "../subscribers.ts";
import {OPEN_EVENT} from "./ConsentModal.ts";
import defineComponent from '../../shared/DefineCustomElement';

defineComponent('cc-settings', class extends HTMLElement {
    // noinspection JSUnusedGlobalSymbols
    connectedCallback() {
        this.setAttribute('role', 'button');
        this.tabIndex = 0;

        this.onclick = () => document.dispatchEvent(new CustomEvent(OPEN_EVENT));
        this.onkeydown = (event) => (event.key === 'Enter' || event.key === ' ') && this.click();

        subscribe((categories: Categories) => {
            this.hidden = categories.size == 0;
        });
    }
});
