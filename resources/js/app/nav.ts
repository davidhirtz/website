const $navItems = document.querySelectorAll('.nav-link') as NodeListOf<HTMLLinkElement>;

export default () => {
    const parts = location.pathname.split('/');
    const urls: string[] = [];

    for (let i = 1; i < parts.length; i++) {
        urls.push(parts.slice(0, i + 1).join('/'));
    }

    $navItems.forEach($item => {
        $item.classList.toggle('active', urls.indexOf($item.getAttribute('href')!) > -1)
    });
}