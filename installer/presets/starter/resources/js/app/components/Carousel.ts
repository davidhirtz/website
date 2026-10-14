import defineComponent from '../shared/DefineCustomElement';

/**
 * Adds previous and next buttons to a row of slides that scrolls and snaps by CSS alone (`_carousel.php`). A button
 * scrolls by one slide and is disabled at its end of the row.
 */
defineComponent('x-carousel', class extends HTMLElement {
    // noinspection JSUnusedGlobalSymbols
    connectedCallback() {
        const $track = this.querySelector<HTMLElement>('[data-track]');
        const $controls = this.querySelector<HTMLElement>('[data-controls]');
        const $prev = this.querySelector<HTMLButtonElement>('[data-prev]');
        const $next = this.querySelector<HTMLButtonElement>('[data-next]');

        if (!$track || !$controls || !$prev || !$next || $track.children.length < 2) {
            return;
        }

        const behavior: ScrollBehavior = window.matchMedia('(prefers-reduced-motion: reduce)').matches ? 'auto' : 'smooth';

        const scroll = (direction: number) => {
            const $slide = $track.firstElementChild as HTMLElement;
            const gap = parseFloat(getComputedStyle($track).columnGap) || 0;

            $track.scrollBy({left: direction * ($slide.offsetWidth + gap), behavior});
        };

        const update = () => {
            $prev.disabled = $track.scrollLeft <= 1;
            $next.disabled = $track.scrollLeft + $track.clientWidth >= $track.scrollWidth - 1;
        };

        $prev.onclick = () => scroll(-1);
        $next.onclick = () => scroll(1);

        $track.addEventListener('scroll', update, {passive: true});
        $controls.classList.replace('hidden', 'flex');

        update();
    }
});
