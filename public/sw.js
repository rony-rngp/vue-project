self.addEventListener('notificationclick', function(event) {
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
});
