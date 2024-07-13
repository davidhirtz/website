import Router from "skeleton-router";

import observe from "./components/observer.ts";

import "../css/app.css";

const doc = document;
const $body = doc.querySelector('body') as HTMLBodyElement;
// const $cookieReset = doc.getElementById('reset');

const router = new Router();

// const addClass = ($el: HTMLElement, className?: string) => $el.classList.add(className || router.a);
const removeClass = ($el: HTMLElement, className?: string) => $el && $el.classList.remove(className || router.a);

const clickHandler = (querySelector: string, callback: EventListenerOrEventListenerObject) =>
    doc.querySelectorAll(querySelector).forEach((el) => {
        el.addEventListener('click', callback);
    });

const resetMenu = () => removeClass($body, hasMenuClass);

const hasMenuClass = 'has-menu';
// const hiddenClass = 'hidden';

clickHandler('.toggle', (e) => {
    const $btn = e.target as HTMLButtonElement;
    const target = $btn.dataset.target;

    if (target) {
        document.querySelectorAll(target).forEach(($target) => {
            $target.classList.toggle(router.a);
        });
    }
});

router.afterRender = () => {
    observe();
};

// if (consent.getCookie()) {
//     removeClass($cookieReset!.parentElement!, hiddenClass);
// }

router.onUnhandledClick = () => {
    router.scrollTo(0);
    resetMenu();
}

router.afterRender();
