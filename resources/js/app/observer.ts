let isFirstRun = true;
const defaultDelay = 120;
const maxQueue = 1000;

/**
 * When the next element of each group may fade in. A `.reveal` container without a `data-reveal-group` is in the
 * global group, whose wait is capped so that nothing waits too long. A named group queues on its own, uncapped, so
 * its elements come in `data-reveal-delay` apart however many there are.
 */
const slots = new Map<string, number>();

const reveal = ($el: HTMLElement, group: string) => {
    const animation = $el.dataset.animation || 'revealed';
    const delay = Number($el.parentElement?.dataset.revealDelay) || defaultDelay;
    const now = performance.now();
    const wait = Math.max((slots.get(group) ?? 0) - now, 0);
    const queue = group ? wait : Math.min(wait, maxQueue);

    setTimeout(() => {
        $el.classList.remove('unrevealed');
        $el.classList.add(animation);
    }, queue);

    slots.set(group, now + queue + delay);
}

const animationObserver = new IntersectionObserver(function (e) {
    for (let i = 0; i < e.length; i++) {
        const entry = e[i];
        const $el = entry.target as HTMLElement;
        const group = $el.parentElement?.dataset.revealGroup ?? '';

        // The observer's first report on an element left visible on page load: out of view, it is hidden until it
        // scrolls in, in view, it stays as rendered. Reading this from the observer spares a forced layout. A named
        // group is hidden by the stylesheet from the start, so it always fades in.
        if (!group && !$el.classList.contains('unrevealed')) {
            if (entry.isIntersecting) {
                animationObserver.unobserve($el);
            } else {
                $el.classList.add('unrevealed');
            }

            continue;
        }

        if (entry.isIntersecting) {
            reveal($el, group);
            animationObserver.unobserve($el);
        }
    }
});

const videoObserver = new IntersectionObserver(function (e) {
    for (let i = 0; i < e.length; i++) {
        const entry = e[i];
        const $el = entry.target as HTMLVideoElement;

        if (entry.isIntersecting) {
            const src = $el.dataset.src;

            if (src) {
                $el.src = src;
            }

            videoObserver.unobserve($el);
        }
    }
});

export default ($container: HTMLElement | Document) => {
    // On page load, everything is left as rendered for the observer to sort out. Content the router swaps in is hidden
    // right away, before the browser paints it, and fades in like the rest.
    const $root = isFirstRun ? document : $container;
    const $elements = $root.querySelectorAll<HTMLElement>('.reveal>*');

    if (!isFirstRun) {
        $elements.forEach($el => $el.classList.add('unrevealed'));
    }

    isFirstRun = false;

    setTimeout(() => {
        $elements.forEach($el => animationObserver.observe($el));
        $root.querySelectorAll<HTMLVideoElement>('[data-src]').forEach($el => videoObserver.observe($el));
    });
}
