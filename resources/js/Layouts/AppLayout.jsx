import { Link } from '@inertiajs/react';

/**
 * bare = the page manages its own full-height layout and scrolling. Used by the
 * compose screen, which pins a WhatsApp-style composer to the bottom of the
 * viewport and therefore cannot sit inside a padded, centred <main>.
 */
export default function AppLayout({ children, bare = false }) {
    if (bare) {
        return <div className="flex h-dvh flex-col bg-stone-50">{children}</div>;
    }

    return (
        <div className="min-h-screen bg-stone-50">
            <header className="border-b border-stone-200 bg-white">
                <div className="mx-auto flex max-w-3xl items-center justify-between gap-3 px-4 py-4 sm:px-6 sm:py-5">
                    <Link href="/" className="text-base font-semibold tracking-tight text-stone-900 sm:text-lg">
                        The City Around You
                    </Link>
                    {/* Redundant on a phone, where the title already fills the bar. */}
                    <span className="hidden text-sm text-stone-500 sm:inline">Karachi civic reporting</span>
                </div>
            </header>

            <main className="mx-auto max-w-3xl px-4 py-8 sm:px-6 sm:py-10">{children}</main>
        </div>
    );
}
