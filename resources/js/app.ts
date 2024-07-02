import Router, {Consent} from "skeleton-router";
import "../css/app.css";

const doc = document;
const $body = doc.querySelector('body') as HTMLBodyElement;
// const $cookieReset = doc.getElementById('reset');

const router = new Router();
const consent = new Consent();

const addClass = ($el: HTMLElement, className?: string) => $el.classList.add(className || router.a);
const removeClass = ($el: HTMLElement, className?: string) => $el && $el.classList.remove(className || router.a);

const clickHandler = (querySelector: string, callback: EventListenerOrEventListenerObject) =>
    doc.querySelectorAll(querySelector).forEach((el) => {
        el.addEventListener('click', callback);
    });

const resetMenu = () => removeClass($body, hasMenuClass);

const hasMenuClass = 'has-menu';
// const hiddenClass = 'hidden';
const observeCssClass = 'observe';

clickHandler('.toggle', (e) => {
    const $btn = e.target as HTMLButtonElement;
    const target = $btn.dataset.target;

    if (target) {
        document.querySelectorAll(target).forEach(($target) => {
            $target.classList.toggle(router.a);
        });
    }
});

const observer = new IntersectionObserver(function (e) {
    for (let i = 0; i < e.length; i++) {
        if (e[i].isIntersecting) {
            const $el = e[i].target as HTMLVideoElement;

            const animation = $el.dataset.animation;
            const delay = $el.dataset.animationDelay;
            const src = $el.dataset.src;

            if (animation) {
                addClass($el, animation);

                if (delay) {
                    $el.style.animationDelay = delay;
                }
            }

            if (src) {
                $el.src = src;
            }

            observer.unobserve($el);
            removeClass($el, observeCssClass);
        }
    }
}, {threshold: [0]});

router.afterRender = () => {
    setTimeout(() =>
            doc.querySelectorAll(`.${observeCssClass}`).forEach($el => observer.observe($el)),
        1
    );
};

// if (consent.getCookie()) {
//     removeClass($cookieReset!.parentElement!, hiddenClass);
// }

console.log(consent);

router.onUnhandledClick = () => {
    router.scrollTo(0);
    resetMenu();
}

router.afterRender();
