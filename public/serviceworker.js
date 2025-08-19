const CACHE_NAME = "pwa-cache-v1";
const BASE_URL = "/gov"; // IMPORTANT: matches your Laravel base path on ngrok

const urlsToCache = [
    `${BASE_URL}/`,
    `${BASE_URL}/offline.html`,
    `${BASE_URL}/assets/img/favicon.png`,
];

// Install
self.addEventListener("install", (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            return Promise.all(
                urlsToCache.map((url) =>
                    fetch(url)
                        .then((response) => {
                            if (response.ok) {
                                return cache.put(url, response);
                            } else {
                                console.warn("Skipping (bad status):", url);
                            }
                        })
                        .catch((err) => {
                            console.warn("Failed to cache:", url, err);
                        })
                )
            );
        })
    );
});

// Fetch
self.addEventListener("fetch", (event) => {
    event.respondWith(
        fetch(event.request)
            .then((response) => {
                if (event.request.method === "GET" && response.ok) {
                    const clone = response.clone();
                    caches
                        .open(CACHE_NAME)
                        .then((cache) => cache.put(event.request, clone));
                }
                return response;
            })
            .catch(() =>
                caches.match(event.request).then((cached) => {
                    if (cached) {
                        return cached;
                    }
                    if (event.request.mode === "navigate") {
                        return caches.match(`${BASE_URL}/offline.html`);
                    }
                })
            )
    );
});

// Activate
self.addEventListener("activate", (event) => {
    event.waitUntil(
        caches.keys().then((names) =>
            Promise.all(
                names.map((name) => {
                    if (name !== CACHE_NAME) {
                        return caches.delete(name);
                    }
                })
            )
        )
    );
});
