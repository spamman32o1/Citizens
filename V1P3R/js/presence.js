(function () {
    const PRESENCE_ENDPOINT = 'H4Z3/presence.php';
    const HEARTBEAT_INTERVAL = 20000; // 20 seconds
    let heartbeatTimer = null;
    let lastVisibilityState = document.visibilityState;

    function sendHeartbeat(options = {}) {
        if (document.visibilityState === 'hidden' && !options.force) {
            return;
        }

        const requestInit = {
            method: 'POST',
            keepalive: true,
            cache: 'no-store',
            credentials: 'same-origin',
        };

        if (options.useBeacon && typeof navigator.sendBeacon === 'function') {
            try {
                navigator.sendBeacon(PRESENCE_ENDPOINT, new Blob([], { type: 'application/json' }));
                return;
            } catch (err) {
                // Fallback to fetch if sendBeacon fails.
            }
        }

        fetch(PRESENCE_ENDPOINT, requestInit).catch(() => {
            // Silently ignore network errors to avoid disrupting the user flow.
        });
    }

    function startHeartbeatTimer() {
        if (heartbeatTimer !== null) {
            window.clearInterval(heartbeatTimer);
        }

        heartbeatTimer = window.setInterval(() => {
            sendHeartbeat();
        }, HEARTBEAT_INTERVAL);
    }

    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && lastVisibilityState !== 'visible') {
            sendHeartbeat();
        }
        lastVisibilityState = document.visibilityState;
    });

    window.addEventListener('pagehide', () => {
        sendHeartbeat({ force: true, useBeacon: true });
    });

    function bootstrap() {
        if (document.visibilityState === 'visible') {
            sendHeartbeat({ force: true });
        }
        startHeartbeatTimer();
    }

    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        bootstrap();
    } else {
        document.addEventListener('DOMContentLoaded', bootstrap, { once: true });
    }
})();

