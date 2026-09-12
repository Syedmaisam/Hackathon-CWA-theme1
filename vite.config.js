import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import { bunny } from 'laravel-vite-plugin/fonts';
import tailwindcss from '@tailwindcss/vite';
import react from '@vitejs/plugin-react';
import { VitePWA } from 'vite-plugin-pwa';
import path from 'node:path';

export default defineConfig({
    resolve: {
        alias: {
            '@': path.resolve(import.meta.dirname, 'resources/js'),
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
            fonts: [
                bunny('Instrument Sans', {
                    weights: [400, 500, 600],
                }),
            ],
        }),
        react(),
        tailwindcss(),

        // Laravel builds into public/build, but a service worker only controls
        // pages at or below its own path, and the manifest is fetched from the
        // document root. Both therefore have to land in public/ itself, which is
        // what outDir plus the base-relative filenames below arrange.
        VitePWA({
            registerType: 'autoUpdate',
            // Laravel's plugin pins Vite's outDir to public/build, so the plugin's
            // own registerSW would register /build/sw.js with scope /build/ — a
            // worker scoped there controls no page the citizen ever visits. The
            // worker is emitted to public/ instead and registered by hand from
            // app.blade.php, at the root scope it actually needs.
            injectRegister: null,
            outDir: 'public',
            filename: 'sw.js',
            // The manifest is a committed static file at public/manifest.webmanifest
            // and app.blade.php links it directly. Generating it here instead emits
            // it as a Vite build asset, which lands under public/build/ whatever
            // outDir says, because Laravel's plugin owns that path. One static file
            // beats two plugins arguing over one URL.
            manifest: false,
            workbox: {
                // The hashed bundles live under public/build; globDirectory is
                // public/ because that is where the worker is emitted.
                globDirectory: 'public',
                globPatterns: ['build/assets/**/*.{js,css,woff,woff2}', 'icons/*.png', 'favicon.svg'],
                // No navigateFallback: it calls createHandlerBoundToURL, which
                // throws non-precached-url unless the fallback document is in the
                // precache. Every page here is server-rendered, so Vite never sees
                // one to precache and the worker would fail on activation.
                //
                // Navigations are handled at runtime instead. Network-first keeps
                // the app always-fresh online while leaving a usable copy of the
                // composer for a cold offline launch.
                runtimeCaching: [
                    {
                        urlPattern: ({ request, url }) =>
                            request.mode === 'navigate' &&
                            ! url.pathname.startsWith('/admin') &&
                            ! url.pathname.startsWith('/storage'),
                        handler: 'NetworkFirst',
                        options: {
                            cacheName: 'pages',
                            networkTimeoutSeconds: 3,
                            expiration: { maxEntries: 30, maxAgeSeconds: 60 * 60 * 24 * 7 },
                            cacheableResponse: { statuses: [200] },
                        },
                    },
                    {
                        // Uploaded report photos, which never change once written.
                        urlPattern: ({ url }) => url.pathname.startsWith('/storage/'),
                        handler: 'CacheFirst',
                        options: {
                            cacheName: 'report-photos',
                            expiration: { maxEntries: 60, maxAgeSeconds: 60 * 60 * 24 * 30 },
                            cacheableResponse: { statuses: [200] },
                        },
                    },
                ],
                // Must be an explicit undefined, not simply omitted: the plugin
                // defaults it to "index.html", a file this app does not have, and
                // the generated handler throws non-precached-url on activation,
                // which takes the whole worker down. Every page here is
                // server-rendered, so there is no document to precache as a
                // fallback. The navigation rule above covers offline instead.
                navigateFallback: undefined,
                cleanupOutdatedCaches: true,
                clientsClaim: true,
                skipWaiting: true,
            },
            devOptions: {
                // Vite serves assets from memory in dev, so a worker there would
                // cache nothing useful and would shadow hot reloads.
                enabled: false,
            },
        }),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
