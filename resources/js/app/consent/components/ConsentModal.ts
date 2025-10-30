import {Categories, setCategories} from "../categories.ts";
import {subscribe} from "../subscribers.ts";
import defineComponent from '../../shared/DefineCustomElement';

defineComponent('cc-modal', class extends HTMLElement {
    // noinspection JSUnusedGlobalSymbols
    connectedCallback() {
        const form = this.querySelector('form') as HTMLFormElement;

        form.onsubmit = (event) => {
            setCategories([...form.elements].filter(($el) => ($el as HTMLInputElement).checked)
                .map(($el) => ($el as HTMLInputElement).dataset.categories)
                .join(','));

            event.preventDefault();
        }

        subscribe((categories: Categories) => {
            this.hidden = categories.size > 0;
        });
    }
});