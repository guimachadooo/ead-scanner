importScripts('https://storage.googleapis.com/workbox-cdn/releases/3.5.0/workbox-sw.js');

if (workbox) {

    workbox.setConfig({
        clientsClaim: true,
        debug: false,
        skipWaiting: true
    });

    workbox.core.setLogLevel(workbox.core.LOG_LEVELS.warn);

    // js / css (up to 10 files / 1 week)
    workbox.routing.registerRoute(
        /^https?:\/\/.*eadplataforma.*\.(js|css)/,
        workbox.strategies.networkFirst({
            cacheName: 'jscss-cache',
            plugins: [
                new workbox.expiration.Plugin({
                    maxEntries: 10,
                    maxAgeSeconds: 7 * 24 * 60 * 60
                })
            ]
        })
    );

    // images (up to 1000 files)
    workbox.routing.registerRoute(
        /^https?:\/\/.*eadplataforma.*\.(?:png|jpg|jpeg|svg|gif)/,
        workbox.strategies.staleWhileRevalidate({
            // Use a custom cache name
            cacheName: 'image-cache',
            plugins: [
                new workbox.expiration.Plugin({
                    maxEntries: 1000
                })
            ]
        })
    );
}