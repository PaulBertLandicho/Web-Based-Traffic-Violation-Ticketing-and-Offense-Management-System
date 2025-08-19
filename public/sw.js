self.addEventListener("install", function (e) {
    e.waitUntil(
        caches.open("ictpmo-cache").then(function (cache) {
            return cache.addAll(["/", "/offline.html"]);
        })
    );
});

self.addEventListener("fetch", function (e) {
    e.respondWith(
        caches.match(e.request).then(function (response) {
            return (
                response ||
                fetch(e.request).catch(() => caches.match("/offline.html"))
            );
        })
    );
});
