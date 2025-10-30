import {setCategories} from "../categories.ts";
import defineComponent from '../../shared/DefineCustomElement';

defineComponent('cc-button', class extends HTMLElement {
    // noinspection JSUnusedGlobalSymbols
    connectedCallback() {
        this.onclick = () => setCategories(this.dataset.categories
            || ([...document.querySelectorAll('[data-categories]')] as Array<HTMLElement>)
                .reduce((categories, $element) => categories + ',' + $element.dataset.categories, '')
                .slice(1)
        );
    }
});