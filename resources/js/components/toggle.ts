const toggleClass = ($el: Element) => $el.classList.toggle('active');

(document.querySelectorAll('.toggle') as NodeListOf<HTMLButtonElement>).forEach(($btn) => {
    $btn.onclick = () => {
        const targetSelector = $btn.dataset.target;
        const closestSelector = $btn.dataset.targetClosest;

        if (targetSelector) {
            document.querySelectorAll(targetSelector).forEach(($target) => {
                toggleClass($target);
            });
        }

        if (closestSelector) {
            const $closest = $btn.closest(closestSelector);

            if ($closest) {
                toggleClass($closest);
            }
        }
    };
});