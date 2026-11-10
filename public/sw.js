self.addEventListener('push', (event) => {
    const payload = event.data ? event.data.json() : {};
    const data = payload.data ?? {};

    event.waitUntil((async () => {
        const windows = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
        const appVisible = windows.some((client) => client.focused);

        await self.registration.showNotification(payload.title ?? 'Pengaduan Baru', {
            body: payload.body ?? '',
            icon: payload.icon ?? '/img/icon-192.png',
            badge: payload.badge ?? '/img/icon-192.png',
            lang: payload.lang ?? 'id',
            tag: payload.tag ?? 'helpdesk-ticket',
            data: { url: data.url ?? '/pwa/helpdesk/pengaduan' },
            requireInteraction: true,
            silent: appVisible,
        });
    })());
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const target = new URL(
        event.notification.data?.url ?? '/pwa/helpdesk/pengaduan',
        self.location.origin,
    ).href;

    event.waitUntil((async () => {
        const windows = await self.clients.matchAll({ type: 'window', includeUncontrolled: true });
        for (const client of windows) {
            if ('focus' in client) {
                await client.focus();
                if ('navigate' in client) await client.navigate(target);
                return;
            }
        }
        await self.clients.openWindow(target);
    })());
});