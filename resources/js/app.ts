import Router, {Consent} from "skeleton-router";

import observe from "./components/observer.ts";
import "./components/toggle.ts";

import "../css/app.css";

const doc = document;
const $body = doc.querySelector('body') as HTMLBodyElement;
// const $cookieReset = doc.getElementById('reset');

const router = new Router();
const consent = new Consent();

// const addClass = ($el: HTMLElement, className?: string) => $el.classList.add(className || router.a);
const removeClass = ($el: HTMLElement, className?: string) => $el && $el.classList.remove(className || router.a);


const resetMenu = () => removeClass($body, hasMenuClass);

const hasMenuClass = 'has-menu';
// const hiddenClass = 'hidden';


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
