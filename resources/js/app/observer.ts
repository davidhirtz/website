const observeCssClass = 'observe';

const removeAttribute = ($el: HTMLElement, attribute: string) => $el.removeAttribute(attribute);

const observer = new IntersectionObserver(function (e) {
    for (let i = 0; i < e.length; i++) {
        const entry = e[i];
        const $el = entry.target as HTMLVideoElement;

        if (entry.isIntersecting) {
            const animation = $el.dataset.animation;
            const delay = $el.dataset.animationDelay;
            const src = $el.dataset.src;

            if (animation) {
                $el.classList.add(animation);
                removeAttribute($el, 'data-animation');

                if (delay) {
                    $el.style.animationDelay = delay;
                    removeAttribute($el, 'data-animation-delay');
                }
            }

            if (src) {
                $el.src = src;
            }

            observer.unobserve($el);
            $el.classList.remove(observeCssClass);
        }
    }
});


export default () => {
    setTimeout(() => {
        document.querySelectorAll(`.${observeCssClass}`).forEach($el => observer.observe($el));
    });
}