// Service Worker for LearnBridge - Offline Support
const CACHE_NAME = 'learnbridge-v1';
const RUNTIME_CACHE = 'learnbridge-runtime-v1';
const STATIC_ASSETS = [
    '/',
    '/index.php',
    '/about_us.php',
    '/contact_us.php',
    '/courses.php',
    '/assets/css/custom.css',
    '/assets/js/main.js',
    '/assets/images/graduation-cap-48 (1).png',
];

// Install event - cache essential assets
self.addEventListener('install', (event) => {
    console.log('[Service Worker] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            console.log('[Service Worker] Caching static assets');
            // Cache what we can - don't fail if some files aren't available
            return Promise.all(
                STATIC_ASSETS.map(url => 
                    cache.add(url).catch(() => {
                        console.log('[Service Worker] Could not cache ' + url);
                    })
                )
            );
        }).catch(err => {
            console.log('[Service Worker] Cache failed:', err);
        })
    );
    self.skipWaiting();
});

// Activate event - clean up old caches
self.addEventListener('activate', (event) => {
    console.log('[Service Worker] Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames.map((cacheName) => {
                    if (cacheName !== CACHE_NAME && cacheName !== RUNTIME_CACHE) {
                        console.log('[Service Worker] Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Fetch event - implement caching strategy
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip cross-origin requests and chrome extensions
    if (url.origin !== location.origin || request.method !== 'GET') {
        return;
    }

    // Network first strategy for API calls (includes .php files)
    if (url.pathname.includes('.php') && !url.pathname.includes('assets')) {
        event.respondWith(networkFirstStrategy(request));
    }
    // Cache first strategy for assets
    else if (url.pathname.includes('assets')) {
        event.respondWith(cacheFirstStrategy(request));
    }
    // Stale while revalidate for HTML pages
    else {
        event.respondWith(staleWhileRevalidateStrategy(request));
    }
});

// Network-first strategy: try network, fall back to cache
function networkFirstStrategy(request) {
    return fetch(request)
        .then((response) => {
            // Only cache successful responses
            if (response.status === 200) {
                const cacheCopy = response.clone();
                caches.open(RUNTIME_CACHE).then((cache) => {
                    cache.put(request, cacheCopy);
                });
            }
            return response;
        })
        .catch(() => {
            return caches.match(request).then((cached) => {
                if (cached) {
                    // Return cached with offline indicator
                    return new Response(
                        addOfflineIndicator(cached.clone()),
                        cached
                    );
                }
                // Return offline fallback page
                return caches.match('/offline.html') 
                    || new Response('Offline - Content not available', { status: 503 });
            });
        });
}

// Cache-first strategy: try cache, fall back to network
function cacheFirstStrategy(request) {
    return caches.match(request).then((cached) => {
        if (cached) {
            return cached;
        }
        return fetch(request).then((response) => {
            if (response.status === 200) {
                const cacheCopy = response.clone();
                caches.open(RUNTIME_CACHE).then((cache) => {
                    cache.put(request, cacheCopy);
                });
            }
            return response;
        }).catch(() => {
            // Return placeholder for missing assets
            if (request.destination === 'image') {
                return createPlaceholderImage();
            }
            return new Response('Resource offline', { status: 503 });
        });
    });
}

// Stale while revalidate strategy
function staleWhileRevalidateStrategy(request) {
    return caches.match(request).then((cached) => {
        const fetchPromise = fetch(request).then((response) => {
            if (response.status === 200) {
                const cacheCopy = response.clone();
                caches.open(RUNTIME_CACHE).then((cache) => {
                    cache.put(request, cacheCopy);
                });
            }
            return response;
        }).catch(() => {
            // Network failed, return cached or offline page
            return cached || caches.match('/offline.html');
        });

        // Return cached while fetching new version
        return cached || fetchPromise;
    });
}

// Create placeholder image for offline mode
function createPlaceholderImage() {
    const svg = `
        <svg xmlns="http://www.w3.org/2000/svg" width="200" height="200">
            <rect fill="#f0f0f0" width="200" height="200"/>
            <text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999" font-size="14">
                Image unavailable (offline)
            </text>
        </svg>
    `;
    return new Response(svg, {
        headers: { 'Content-Type': 'image/svg+xml' }
    });
}

// Add offline indicator to cached content
function addOfflineIndicator(response) {
    // Simple text response - just return as is
    if (response.headers.get('content-type')?.includes('text/plain')) {
        return response;
    }
    return response;
}

// Handle background sync for offline submissions
self.addEventListener('sync', (event) => {
    console.log('[Service Worker] Background sync:', event.tag);
    if (event.tag === 'sync-messages') {
        event.waitUntil(syncMessages());
    }
});

// Sync pending messages when back online
function syncMessages() {
    return new Promise((resolve) => {
        // This would sync queued messages/enrollments when connection returns
        resolve();
    });
}
