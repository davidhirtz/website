import {addCategories, setCategories} from "../categories.ts";
import defineComponent from '../../shared/DefineCustomElement';

const split = (value: string) => value.split(',');

defineComponent('cc-button', class extends HTMLElement {
    // noinspection JSUnusedGlobalSymbols
    connectedCallback() {
        this.onclick = () => {
            if (this.dataset.addCategories) {
                addCategories(split(this.dataset.addCategories));
                return;
            }

            setCategories(this.dataset.categories
                ? split(this.dataset.categories)
                : [...document.querySelectorAll<HTMLInputElement>('cc-modal input[data-categories]')]
                    .flatMap(($input) => split($input.dataset.categories!)));
        }
    }
});
