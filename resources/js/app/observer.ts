let queue = 0;
let isFirstRun = true;
const delay = 120;

const observer = new IntersectionObserver(function (e) {
    for (let i = 0; i < e.length; i++) {
        const entry = e[i];
        const $el = entry.target as HTMLVideoElement;

        if (entry.isIntersecting) {
            const animation = $el.dataset.animation || 'revealed';
            const src = $el.dataset.src;

            setTimeout(() => {
                $el.classList.add(animation)
                queue = Math.max(queue - delay, 0);
            }, queue);

            queue = Math.min(queue + delay, 1000);

            if (src) {
                $el.src = src;
            }

            observer.unobserve($el);
        }
    }
});


export default ($container: HTMLElement | Document) => {
    setTimeout(() => {
        if (isFirstRun) {
            $container = document;
            isFirstRun = false;
        }

        $container.querySelectorAll('.reveal>*').forEach($el => observer.observe($el));
    });
}