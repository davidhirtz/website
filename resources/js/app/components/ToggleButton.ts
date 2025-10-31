import defineComponent from '../shared/DefineCustomElement';

const toggleElement = ($element: HTMLElement) => {
    $element.classList.toggle('active');
}

defineComponent('x-toggle', class extends HTMLElement {
    // noinspection JSUnusedGlobalSymbols
    connectedCallback() {
        this.onclick = () => {
            const targetDataId = this.dataset.targetId;

            if (targetDataId) {
                (document.querySelectorAll(`[data-id="${targetDataId}"]`) as NodeListOf<HTMLElement>).forEach(($target) => {
                    toggleElement($target);
                });

                return;
            }

            const $group: HTMLElement | null = this.closest('.group');

            if ($group) {
                toggleElement($group);
            }
        }
    }
});