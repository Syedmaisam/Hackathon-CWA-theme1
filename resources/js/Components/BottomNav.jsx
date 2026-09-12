import { Link, usePage } from '@inertiajs/react';
import Icon from '@/Components/Icon';

const TABS = [
    { label: 'Report', href: '/', icon: 'chat' },
    { label: 'Help', href: '/help', icon: 'help' },
];

/**
 * Sits inside the app shell's flex column rather than being position-fixed, so
 * the iOS keyboard cannot cover it and it never drifts during scroll.
 */
export default function BottomNav() {
    const { url } = usePage();

    // A report page keeps the Report tab lit — it is the outcome of composing
    // one, not a separate place in the app.
    function isActive(href) {
        return href === '/' ? url === '/' || url.startsWith('/reports') : url.startsWith(href);
    }

    return (
        <nav
            aria-label="Main"
            className="shrink-0 border-t border-stone-200 bg-white pb-[env(safe-area-inset-bottom)] select-none"
        >
            {/* Centred rather than stretched to 50% each: two tabs spread across a
                full phone width read as an unfinished bar. */}
            <div className="mx-auto flex max-w-xs items-stretch justify-around">
                {TABS.map((tab) => {
                    const active = isActive(tab.href);

                    return (
                        <Link
                            key={tab.href}
                            href={tab.href}
                            aria-current={active ? 'page' : undefined}
                            className={`flex min-h-14 flex-1 flex-col items-center justify-center gap-1 px-4 transition-colors ${
                                active ? 'text-accent-700' : 'text-stone-400 hover:text-stone-600'
                            }`}
                        >
                            <Icon name={tab.icon} className="h-6 w-6" strokeWidth={active ? 2 : 1.5} />
                            <span className={`text-[11px] leading-none ${active ? 'font-semibold' : 'font-medium'}`}>
                                {tab.label}
                            </span>
                        </Link>
                    );
                })}
            </div>
        </nav>
    );
}
