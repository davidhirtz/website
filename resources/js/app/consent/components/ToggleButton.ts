import defineComponent from '../../shared/DefineCustomElement';

defineComponent('x-toggle', class extends HTMLElement {
    // noinspection JSUnusedGlobalSymbols
    connectedCallback() {
        const $btn = this;

        $btn.onclick = () => {
            const targetDataId = $btn.dataset.targetId;

            if (targetDataId) {
                (document.querySelectorAll(`[data-id="${targetDataId}"]`) as NodeListOf<HTMLElement>).forEach(($target) => {
                    $btn.toggleElement($target);
                });

                return;
            }

            const $group: HTMLElement | null = $btn.closest('.group');

            if ($group) {
                $btn.toggleElement($group);
            }
        }
    }

    toggleElement = ($element: HTMLElement) => {
        $element.classList.toggle('active');
    }
});