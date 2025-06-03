/*self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    event.waitUntil(
        self.clients.matchAll().then(function(clients) {
            clients.forEach(client => {
                if (event.action === 'answer') {
                    client.postMessage({ type: 'ANSWER_CALL' });
                } else if (event.action === 'reject') {
                    client.postMessage({ type: 'REJECT_CALL' });
                } else {
                    client.focus();
                }
            });
        })
    );
});*/

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then(async (clients) => {
            // Change this URL to the page you want to focus/open on notification click
            const callPageUrl = '/admin/sip'; // <-- update this!

            let client = clients.find(c => c.url.includes(callPageUrl) && 'focus' in c);

            if (!client && clients.length > 0) {
                client = clients[0];
            }

            if (client) {
                await client.focus();
            } else {
                client = await self.clients.openWindow(callPageUrl);
                // Optional: small delay to let page load
                await new Promise(resolve => setTimeout(resolve, 500));
            }

            if (event.action === 'answer') {
                client.postMessage({ type: 'ANSWER_CALL' });
            } else if (event.action === 'reject') {
                client.postMessage({ type: 'REJECT_CALL' });
            }
        })
    );
});
