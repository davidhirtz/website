import {categories, subscribe} from "./consent.ts";

let activate: Function | null
let reset: Function | null;

export default ($container: HTMLElement) => {
    activate = null;
    reset = null;

    $container.querySelectorAll<HTMLButtonElement>('[data-embed-src]').forEach(($btn) => {
        $btn.onclick = () => {
            const $embed = $btn.closest<HTMLElement>('[data-embed]')!;
            const $iframe = $embed.querySelector<HTMLIFrameElement>('iframe')!;

            $embed.classList.add('embedded')

            if (reset) {
                reset();
            }

            activate = () => {
                if (categories.has('external')) {
                    const $cc = $embed.querySelector<HTMLElement>('[data-embed-cc]');

                    if ($cc) {
                        $cc.remove();
                    }

                    $iframe.src = $btn.dataset.embedSrc!;
                    $iframe.hidden = false;
                }
            }

            reset = () => {
                if ($iframe) {
                    $iframe.src = '';
                    $iframe.hidden = true;
                    $embed.classList.remove('embedded');
                }
            }

            activate();
        }
    });
}

subscribe((categories) => {
    if (categories.has('external') && activate) {
        activate();
    }
});