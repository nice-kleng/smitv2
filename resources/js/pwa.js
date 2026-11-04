const menu = document.getElementById('menu-sheet');
const menuButton = document.getElementById('menu-button');

if (menu && menuButton) {
    menuButton.addEventListener('click', () => {
        menu.showModal();
        menuButton.setAttribute('aria-expanded', 'true');
    });
    menu.addEventListener('close', () => menuButton.setAttribute('aria-expanded', 'false'));
    menu.addEventListener('click', (event) => {
        if (event.target === menu) menu.close();
    });
    document.getElementById('menu-close')?.addEventListener('click', () => menu.close());
}

const timeFormat = new Intl.DateTimeFormat('id-ID', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
});

document.querySelectorAll('[data-local-time]').forEach((element) => {
    const date = new Date(element.getAttribute('datetime'));
    if (!Number.isNaN(date.getTime())) element.textContent = timeFormat.format(date);
});

const dateTimeFormat = new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short',
});

document.querySelectorAll('[data-local-datetime]').forEach((element) => {
    const date = new Date(element.getAttribute('datetime'));
    if (!Number.isNaN(date.getTime())) element.textContent = dateTimeFormat.format(date);
});

const search = document.getElementById('queue-search');
if (search) {
    const items = [...document.querySelectorAll('[data-ticket-item]')];
    const empty = document.getElementById('search-empty');
    const applyFilter = () => {
        const query = search.value.trim().toLowerCase();
        let visible = 0;
        items.forEach((item) => {
            const match = query === '' || item.dataset.search.includes(query);
            item.hidden = !match;
            if (match) visible++;
        });
        if (empty) empty.hidden = visible > 0;
    };
    const params = new URLSearchParams(window.location.search);
    search.value = params.get('q') ?? '';
    applyFilter();
    search.addEventListener('input', () => {
        const next = new URLSearchParams(window.location.search);
        if (search.value.trim()) next.set('q', search.value.trim());
        else next.delete('q');
        const query = next.toString();
        window.history.replaceState(null, '', window.location.pathname + (query ? `?${query}` : ''));
        applyFilter();
    });
}