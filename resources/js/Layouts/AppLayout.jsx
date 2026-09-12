import { Link } from '@inertiajs/react';

export default function AppLayout({ children }) {
    return (
        <div className="min-h-screen bg-stone-50">
            <header className="border-b border-stone-200 bg-white">
                <div className="mx-auto flex max-w-3xl items-center justify-between px-6 py-5">
                    <Link href="/" className="text-lg font-semibold tracking-tight text-stone-900">
                        The City Around You
                    </Link>
                    <span className="text-sm text-stone-500">Karachi civic reporting</span>
                </div>
            </header>

            <main className="mx-auto max-w-3xl px-6 py-10">{children}</main>
        </div>
    );
}
