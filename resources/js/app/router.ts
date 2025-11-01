export default (parseContent: ($container: HTMLElement) => void) => {
    const $container = document.querySelector('main') as HTMLElement;

    const cache: Map<string, string> = new Map();
    const scrollPositions: Map<string, { x: number, y: number }> = new Map();

    const loadPage = (useCache: boolean = true) => {
        if (useCache && cache.has(location.href)) {
            renderContent();
            return;
        }

        fetching = true;

        fetchUrl(location.href).then(async response => {
            if (response.status >= 300 && response.status < 400) {
                const redirect = response.headers.get('X-Redirect');

                if (redirect) {
                    if (redirect.indexOf(location.host) === -1) {
                        location.replace(redirect);
                    } else {
                        history.replaceState(null, document.title, redirect);
                        loadPage(false);
                    }
                }
            } else {
                await cacheResponse(location.href, response);
                renderContent();
            }

            fetching = false;
        });
    }

    const attachPreloadOnHoverEvents = ($el: HTMLElement) => {
        if (window.matchMedia('(hover:none)').matches) {
            return;
        }

        $el.querySelectorAll('a').forEach($link => {
            if (isInvalidLinkTag($link)) {
                return;
            }

            const url = new URL($link.href);

            const onHover = () => {
                if (!isSamePage(url) && !cache.has(url.href)) {
                    hoverTimeoutId = setTimeout(() => {
                        if (!fetching) {
                            fetchUrl(url.href).then(response => {
                                if (response.ok) {
                                    void cacheResponse(url.href, response);
                                } else {
                                    $link.removeEventListener('mouseenter', onHover);
                                }
                            });
                        }
                    }, 200);
                }
            }

            $link.addEventListener('mouseenter', onHover);
            $link.addEventListener('mouseleave', () => clearTimeout(hoverTimeoutId));
        });
    }

    const fetchUrl = (url: string) => {
        return fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-Ajax-Request': 'route',
            },
        });
    }

    const cacheResponse = async (url: string, response: Response) => {
        cache.set(url, await response.text());

        if (cache.size > 10) {
            const firstKey = cache.keys().next().value!;
            cache.delete(firstKey);
        }
    }

    const renderContent = () => {
        const html = cache.get(location.href);
        const $el = $container;

        if (!html || html.trim().startsWith('<!DOC')) {
            location.reload();
            return;
        }

        scrollPositions.set(referrer, {
            x: window.scrollX,
            y: window.scrollY,
        });

        $el.innerHTML = html;

        normalizeScriptTags($el);
        parseContent($el);
        attachPreloadOnHoverEvents($container);

        const hasPrevPos = isPopState && scrollPositions.has(location.href);
        const position = hasPrevPos ? scrollPositions.get(location.href)! : {x: 0, y: 0};

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

    const isInvalidLinkTag = ($link: HTMLAnchorElement) => {
        const url = $link.href ? new URL($link.href) : false;
        const target = $link.getAttribute('target') || '_self';

        return !url
            || $link.hasAttribute('download')
            || $link.hasAttribute('data-refresh')
            || target !== '_self'
            || url.host !== location.host
    }

    const isSamePage = (url: URL) => {
        return url.pathname === location.pathname && url.search === location.search;
    }

    let isPopState: boolean = false;
    let referrer: string = location.href;
    let hoverTimeoutId: number;
    let fetching: boolean = false;

    window.addEventListener('popstate', (e: PopStateEvent) => {
        isPopState = true;
        loadPage();

        e.preventDefault();
    });

    document.addEventListener('click', (e: MouseEvent) => {
        let $el = e.target as HTMLElement | null;

        while ($el) {
            if ($el.matches('a')) {
                const $link = ($el as HTMLAnchorElement);

                if (
                    e.defaultPrevented
                    || e.ctrlKey
                    || e.metaKey
                    || e.shiftKey
                    || isInvalidLinkTag($link)
                ) {
                    return;
                }

                e.preventDefault();

                const url = new URL($link.href);

                if (isSamePage(url)) {
                    scrollToPath(url);
                } else {
                    history.pushState(null, $link.title || document.title, $link.href);
                    loadPage();
                }

                return;
            }

            $el = $el.parentElement;
        }
    });

    parseContent($container);
    attachPreloadOnHoverEvents(document.body);
};