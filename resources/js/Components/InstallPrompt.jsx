import { useEffect, useState } from 'react';
import Icon, { AppMark } from '@/Components/Icon';

const DISMISSED_KEY = 'install-prompt-dismissed';

function isStandalone() {
    return (
        window.matchMedia?.('(display-mode: standalone)').matches ||
        // iOS predates the display-mode media query for home-screen launches.
        window.navigator.standalone === true
    );
}

function isIos() {
    return /iphone|ipad|ipod/i.test(window.navigator.userAgent);
}

/**
 * Offers to install the app to the home screen.
 *
 * Being installable and actually getting installed are different things: Chrome
 * only surfaces its own install affordance behind a menu most people never open.
 *
 * iOS fires no beforeinstallprompt at all and has no programmatic install, so
 * there it degrades to a one-line hint naming the two taps that do work.
 */
export default function InstallPrompt() {
    const [deferred, setDeferred] = useState(null);
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        // localStorage throws in some private modes, and a failed read must never
        // take the page down with it.
        let dismissed = false;

        try {
            dismissed = window.localStorage.getItem(DISMISSED_KEY) === '1';
        } catch {
            dismissed = false;
        }

        if (dismissed || isStandalone()) {
            return undefined;
        }

        if (isIos()) {
            setVisible(true);

            return undefined;
        }

        function onBeforeInstall(event) {
            // Chrome would otherwise show its own mini-infobar.
            event.preventDefault();
            setDeferred(event);
            setVisible(true);
        }

        function onInstalled() {
            setVisible(false);
            remember();
        }

        window.addEventListener('beforeinstallprompt', onBeforeInstall);
        window.addEventListener('appinstalled', onInstalled);

        return () => {
            window.removeEventListener('beforeinstallprompt', onBeforeInstall);
            window.removeEventListener('appinstalled', onInstalled);
        };
    }, []);

    function remember() {
        try {
            window.localStorage.setItem(DISMISSED_KEY, '1');
        } catch {
            // A citizen with storage blocked sees this again next visit. Harmless.
        }
    }

    function dismiss() {
        setVisible(false);
        remember();
    }

    async function install() {
        if (!deferred) {
            return;
        }

        deferred.prompt();
        await deferred.userChoice;
        setDeferred(null);
        setVisible(false);
        remember();
    }

    if (!visible) {
        return null;
    }

    return (
        <div className="shrink-0 border-b border-accent-100 bg-accent-50 px-4 py-2.5">
            <div className="mx-auto flex max-w-2xl items-center gap-3">
                <span className="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-accent-600 text-white">
                    <AppMark className="h-5 w-5" />
                </span>

                <div className="min-w-0 flex-1">
                    <p className="text-xs font-medium text-stone-900">Install for one-tap reporting</p>
                    {deferred ? (
                        <p className="truncate text-xs text-stone-600">Adds it to your home screen.</p>
                    ) : (
                        <p className="flex items-center gap-1 truncate text-xs text-stone-600">
                            Tap
                            <Icon name="share" className="h-3.5 w-3.5" />
                            then Add to Home Screen.
                        </p>
                    )}
                </div>

                {deferred && (
                    <button
                        type="button"
                        onClick={install}
                        className="min-h-9 shrink-0 rounded-full bg-accent-600 px-4 text-xs font-semibold text-white transition hover:bg-accent-700"
                    >
                        Install
                    </button>
                )}

                <button
                    type="button"
                    onClick={dismiss}
                    aria-label="Dismiss install prompt"
                    className="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-stone-500 transition hover:bg-white/70"
                >
                    <Icon name="x-mark" className="h-4 w-4" />
                </button>
            </div>
        </div>
    );
}
