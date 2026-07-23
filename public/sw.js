/**
 * Service Worker - AgriNex Smart Drip
 * Offline Strategy: Cache-First with Network Fallback
 */

const CACHE_VERSION = 'agrinex-v1.0.0';
const CACHE_STATIC = `${CACHE_VERSION}-static`;
const CACHE_DYNAMIC = `${CACHE_VERSION}-dynamic`;
const CACHE_API = `${CACHE_VERSION}-api`;

// Static assets to cache on install
const STATIC_ASSETS = [
    '/',
    '/offline.html',
    '/build/assets/app-C2ARV1qV.css',
    '/build/assets/app-CKLqfVGG.js',
    '/favicon.ico',
];

// API endpoints to cache (with TTL)
const API_CACHE_DURATION = 5 * 60 * 1000; // 5 minutes

// Install event - cache static assets
self.addEventListener('install', (event) => {
    console.log('[SW] Installing service worker...');
    
    event.waitUntil(
        caches.open(CACHE_STATIC).then((cache) => {
            console.log('[SW] Caching static assets');
            return cache.addAll(STATIC_ASSETS.map(url => new Request(url, {credentials: 'same-origin'})));
        }).catch(err => {
            console.warn('[SW] Failed to cache some assets:', err);
        })
    );
    
    self.skipWaiting();
});

// Activate event - clean old caches
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating service worker...');
    
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter(name => name.startsWith('agrinex-') && name !== CACHE_STATIC && name !== CACHE_DYNAMIC && name !== CACHE_API)
                    .map(name => {
                        console.log('[SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        })
    );
    
    self.clients.claim();
});

// Fetch event - cache strategy
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);
    
    // Skip non-GET requests
    if (request.method !== 'GET') {
        return;
    }
    
    // Skip chrome extensions
    if (url.protocol === 'chrome-extension:') {
        return;
    }
    
    // Skip OAuth routes (Google/social login redirects)
    if (url.pathname.startsWith('/auth/')) {
        return; // Let browser handle OAuth naturally
    }
    
    // API requests - Network First with cache fallback
    if (url.pathname.startsWith('/api/')) {
        event.respondWith(
            networkFirstStrategy(request, CACHE_API)
        );
        return;
    }
    
    // Static assets - Cache First
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.includes('.css') ||
        url.pathname.includes('.js') ||
        url.pathname.includes('.jpg') ||
        url.pathname.includes('.png') ||
        url.pathname.includes('.svg')
    ) {
        event.respondWith(
            cacheFirstStrategy(request, CACHE_STATIC)
        );
        return;
    }
    
    // HTML pages - Network First with cache fallback
    const acceptHeader = request.headers.get('accept') || '';
    if (acceptHeader.includes('text/html')) {
        event.respondWith(
            networkFirstStrategy(request, CACHE_DYNAMIC)
        );
        return;
    }
    
    // Default - Network First
    event.respondWith(
        networkFirstStrategy(request, CACHE_DYNAMIC)
    );
});

/**
 * Cache First Strategy
 * Try cache first, if miss then fetch from network and cache
 */
async function cacheFirstStrategy(request, cacheName) {
    const acceptHeader = request.headers.get('accept') || '';
    try {
        // Try cache first
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Cache miss - fetch from network
        const networkResponse = await fetch(request);
        
        // Cache successful response
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            cache.put(request, networkResponse.clone());
        }
        
        return networkResponse;
    } catch (error) {
        console.error('[SW] Cache first failed:', error);
        
        // Try cache as fallback
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            return cachedResponse;
        }
        
        // Return offline page for HTML requests
        if (acceptHeader.includes('text/html')) {
            const offlineResp = await caches.match('/offline.html');
            if (offlineResp) return offlineResp;
            return new Response('<h1>Offline</h1><p>Tidak ada koneksi internet.</p>', {
                status: 503,
                headers: { 'Content-Type': 'text/html; charset=utf-8' }
            });
        }
        
        return new Response(JSON.stringify({ error: 'Network error' }), {
            status: 503,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

/**
 * Network First Strategy
 * Try network first, if fail then use cache
 */
async function networkFirstStrategy(request, cacheName) {
    const acceptHeader = request.headers.get('accept') || '';
    try {
        // Try network first
        const networkResponse = await fetch(request.clone());
        
        // Cache successful response
        if (networkResponse.ok) {
            const cache = await caches.open(cacheName);
            
            // Add timestamp for API cache expiration
            const clonedResponse = networkResponse.clone();
            const responseToCache = new Response(clonedResponse.body, {
                status: clonedResponse.status,
                statusText: clonedResponse.statusText,
                headers: clonedResponse.headers,
            });
            
            cache.put(request, responseToCache);
        }
        
        return networkResponse;
    } catch (error) {
        // Only log if it's not a common network error
        if (!(error instanceof TypeError)) {
            console.warn('[SW] Network failed, trying cache:', error);
        }
        
        // Network failed - try cache
        const cachedResponse = await caches.match(request);
        
        if (cachedResponse) {
            // Check if API cache is expired (5 minutes)
            if (cacheName === CACHE_API) {
                const dateHeader = cachedResponse.headers.get('date');
                if (dateHeader) {
                    const cachedTime = new Date(dateHeader).getTime();
                    const now = Date.now();
                    
                    if (now - cachedTime > API_CACHE_DURATION) {
                        console.warn('[SW] API cache expired');
                    } else {
                        return cachedResponse;
                    }
                } else {
                    return cachedResponse;
                }
            } else {
                return cachedResponse;
            }
        }
        
        // No cache available - return offline page for HTML
        if (acceptHeader.includes('text/html')) {
            const offlineResp = await caches.match('/offline.html');
            if (offlineResp) return offlineResp;
            return new Response('<h1>Offline</h1><p>Tidak ada koneksi internet.</p>', {
                status: 503,
                headers: { 'Content-Type': 'text/html; charset=utf-8' }
            });
        }
        
        return new Response(JSON.stringify({ error: 'Offline' }), {
            status: 503,
            headers: { 'Content-Type': 'application/json' }
        });
    }
}

// Background sync for offline actions
self.addEventListener('sync', (event) => {
    console.log('[SW] Background sync:', event.tag);
    
    if (event.tag === 'sync-offline-data') {
        event.waitUntil(syncOfflineData());
    }
});

async function syncOfflineData() {
    console.log('[SW] Syncing offline data...');
    
    // TODO: Implement offline data sync
    // Read from IndexedDB and POST to server
}

// Push notification handler
self.addEventListener('push', (event) => {
    console.log('[SW] Push notification received');
    
    let data = {};
    
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = { title: 'AgriNex', body: event.data.text() };
        }
    }
    
    const title = data.title || 'AgriNex Smart Drip';
    const options = {
        body: data.body || 'New notification',
        icon: '/icon.svg',
        badge: '/favicon.ico',
        data: data.data || {},
        vibrate: [200, 100, 200],
        requireInteraction: false,
    };
    
    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

// Notification click handler
self.addEventListener('notificationclick', (event) => {
    console.log('[SW] Notification clicked');
    
    event.notification.close();
    
    // Navigate to app
    event.waitUntil(
        clients.openWindow('/')
    );
});
