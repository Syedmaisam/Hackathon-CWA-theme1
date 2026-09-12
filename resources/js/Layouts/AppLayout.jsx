import BottomNav from '@/Components/BottomNav';
import InstallPrompt from '@/Components/InstallPrompt';

/**
 * The app shell.
 *
 * Fixed to the viewport height with only the content region scrolling. That one
 * property is most of what makes an installed PWA feel native: the page itself
 * never scrolls, so there is no rubber-band overscroll revealing the browser
 * behind it and no address-bar collapse jank mid-gesture.
 *
 * The tab bar lives inside this flex column rather than being position-fixed,
 * so the iOS keyboard cannot cover it.
 *
 * `header` and `footer` are rendered outside the scroll region and stay put.
 * The compose screen uses `footer` for its composer bar; the report screen uses
 * it for the sticky send actions.
 */
export default function AppLayout({ children, header = null, footer = null, padded = true }) {
    return (
        <div className="flex h-dvh flex-col overflow-hidden bg-stone-50">
            <InstallPrompt />

            {header}

            {/* Extra bottom padding when a footer is present: the last section
                would otherwise sit underneath the sticky action bar and read as
                cut off at the end of the scroll. */}
            <main
                className={`min-h-0 flex-1 overflow-y-auto overscroll-contain ${
                    padded ? `px-4 pt-5 ${footer ? 'pb-8' : 'pb-5'}` : ''
                }`}
            >
                {padded ? <div className="mx-auto max-w-2xl">{children}</div> : children}
            </main>

            {footer}

            <BottomNav />
        </div>
    );
}
