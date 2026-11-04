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

const actionForm = document.getElementById('tindakan-form');
if (actionForm) {
    const serviceFields = actionForm.querySelector('[data-service-fields]');
    const serviceInputs = serviceFields ? [...serviceFields.querySelectorAll('input')] : [];
    const saveButton = document.getElementById('save-tindakan');
    const formToast = document.getElementById('form-toast');

    const syncServiceFields = () => {
        const isService = actionForm.querySelector('input[name="keterangan_perbaikan"]:checked')?.value === '4';
        if (serviceFields) serviceFields.hidden = !isService;
        serviceInputs.forEach((input) => { input.disabled = !isService; });
    };

    actionForm.querySelectorAll('input[name="keterangan_perbaikan"]')
        .forEach((radio) => radio.addEventListener('change', syncServiceFields));
    syncServiceFields();

    const showFormError = (message) => {
        if (!formToast) return;
        formToast.textContent = message;
        formToast.hidden = false;
    };

    actionForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        actionForm.querySelectorAll('[data-error-for]').forEach((element) => {
            element.textContent = '';
        });
        actionForm.querySelectorAll('[aria-invalid="true"]').forEach((element) => {
            element.removeAttribute('aria-invalid');
        });
        saveButton.disabled = true;
        const originalLabel = saveButton.textContent;
        saveButton.textContent = 'Menyimpan…';

        try {
            const response = await fetch(actionForm.action, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: new FormData(actionForm),
            });

            if (response.status === 422) {
                const data = await response.json();
                let firstInvalid = null;
                Object.entries(data.errors ?? {}).forEach(([field, messages]) => {
                    const error = actionForm.querySelector(`[data-error-for="${field}"]`);
                    if (error) error.textContent = messages[0];
                    const input = actionForm.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.setAttribute('aria-invalid', 'true');
                        firstInvalid ??= input;
                    }
                });
                firstInvalid?.focus();
                showFormError('Periksa kembali field yang ditandai.');
                return;
            }

            if (!response.ok) throw new Error('save failed');
            window.location.assign(`${actionForm.dataset.detailUrl}?saved=1`);
        } catch (error) {
            showFormError('Tindakan gagal disimpan. Periksa koneksi, lalu coba lagi.');
        } finally {
            saveButton.disabled = false;
            saveButton.textContent = originalLabel;
        }
    });
}