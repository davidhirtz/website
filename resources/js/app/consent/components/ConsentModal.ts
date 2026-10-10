import {categories, Categories, setCategories} from "../categories.ts";
import {subscribe} from "../subscribers.ts";
import defineComponent from '../../shared/DefineCustomElement';

export const OPEN_EVENT = 'cc:open';

defineComponent('cc-modal', class extends HTMLElement {
    // noinspection JSUnusedGlobalSymbols
    connectedCallback() {
        const form = this.querySelector('form') as HTMLFormElement;
        const inputs = [...form.querySelectorAll<HTMLInputElement>('input[data-categories]')];

        form.onsubmit = (event) => {
            event.preventDefault();

            setCategories(inputs
                .filter(($input) => $input.checked)
                .map(($input) => $input.dataset.categories!));
        }

        document.addEventListener(OPEN_EVENT, () => {
            inputs.forEach(($input) => $input.checked = $input.disabled || categories.has($input.dataset.categories!));

            this.querySelectorAll<HTMLElement>('[data-id="cc-tab"]')
                .forEach(($tab) => $tab.classList.toggle('active', $tab === form));

            this.hidden = false;
            inputs.find(($input) => !$input.disabled)?.focus();
        });

        subscribe((categories: Categories) => {
            this.hidden = categories.size > 0;
        });
    }
});
