// noinspection JSUnusedGlobalSymbols

declare global {
    interface Window {
        dataLayer: IArguments[];
        gtag: Gtag.Gtag;
    }
}

export default (tagId: string): void => {
    const script = 'script';
    const $firstTag = document.getElementsByTagName(script)[0] as HTMLScriptElement;
    const $tag = document.createElement(script) as HTMLScriptElement;

    const gtag: Gtag.Gtag = function gtag() {
        window.dataLayer.push(arguments);
    }

    window.dataLayer = window.dataLayer || [];

    gtag('js', new Date());
    gtag('config', tagId);

    $tag.async = true;
    $tag.src = `https://www.googletagmanager.com/gtag/js?id=${tagId}`;
    $firstTag.parentNode!.insertBefore($tag, $firstTag);
}