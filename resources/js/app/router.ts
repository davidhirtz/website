export default (render: ($container: HTMLElement) => void) => {
    const $container = document.querySelector('main') as HTMLElement;

    const cache: Map<string, string> = new Map();
    const positions: Map<string, { x: number, y: number }> = new Map();

    const fetchContent = (useCache: boolean = true) => {
        if (useCache && cache.has(location.href)) {
            renderContent();
            return;
        }

        void fetch(location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-Ajax-Request': 'route',
            },
        }).then(async response => {
            if (response.status >= 300 && response.status < 400) {
                const redirect = response.headers.get('X-Redirect');

                if (redirect) {
                    if (redirect.indexOf(location.host) === -1) {
                        location.replace(redirect);
                    } else {
                        history.replaceState(null, document.title, redirect);
                        fetchContent(false);
                    }
                }
            } else {
                cache.set(location.href, await response.text());

                if (cache.size > 10) {
                    const firstKey = cache.keys().next().value!;
                    cache.delete(firstKey);
                }

                renderContent();
            }
        });
    }

    const renderContent = () => {
        const html = cache.get(location.href);
        const $el = $container;

        if (!html || html.trim().startsWith('<!DOC')) {
            location.reload();
            return;
        }

        positions.set(referrer, {
            x: window.scrollX,
            y: window.scrollY,
        });

        $el.innerHTML = html;

        normalizeScriptTags($el);
        render($el);

        const hasPrevPos = isPopState && positions.has(location.href);
        const position = hasPrevPos ? positions.get(location.href)! : {x: 0, y: 0};

        window.scrollTo(position.x, position.y);
        scrollToPath(location);

        referrer = location.href;
        isPopState = false;
    }

    const normalizeScriptTags = ($el: HTMLElement) => {

        (Array.from($el.querySelectorAll('script')) as Array<HTMLElement>).forEach(oldScript => {
            const newScript = document.createElement('script');

            Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
            newScript.appendChild(document.createTextNode(oldScript.innerHTML));

            oldScript.parentNode!.replaceChild(newScript, oldScript);
        });
    }

    const scrollToPath = (path: Location | URL) => {
        const $target = path.hash ? document.getElementById(path.hash.substring(1)) : null;

        if ($target) {
            scroll({
                top: ($target.offsetTop ?? 0),
                behavior: "smooth"
            });
        }
    }

    let isPopState: boolean = false;
    let referrer: string = location.href;

    window.addEventListener('popstate', (e: PopStateEvent) => {
        isPopState = true;
        fetchContent();

        e.preventDefault();
    });

    document.addEventListener('click', (e: MouseEvent) => {
        let $el = e.target as HTMLElement | null;

        while ($el) {
            if ($el.matches('a')) {
                const $link = ($el as HTMLLinkElement);
                const url = $link.href ? new URL($link.href) : false;
                const target = $link.getAttribute('target') || '_self';

                if (
                    e.defaultPrevented
                    || e.ctrlKey
                    || e.metaKey
                    || e.shiftKey
                    || !url
                    || $link.hasAttribute('download')
                    || $link.hasAttribute('data-refresh')
                    || target !== '_self'
                    || url.host !== location.host
                ) {
                    return;
                }

                e.preventDefault();

                if (url.pathname !== location.pathname || url.search !== location.search) {
                    history.pushState(null, $link.title || document.title, $link.href);
                    fetchContent();
                } else {
                    scrollToPath(url);
                }

                return;
            }

            $el = $el.parentElement;
        }
    });

    render($container);
};