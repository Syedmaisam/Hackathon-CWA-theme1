import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/Layouts/AppLayout';
import Icon from '@/Components/Icon';

function Section({ title, children }) {
    return (
        <section className="mt-6 first:mt-0">
            <h2 className="px-1 pb-2 text-xs font-semibold tracking-wide text-stone-500 uppercase">{title}</h2>
            <div className="space-y-3 rounded-2xl bg-white p-4 text-sm leading-relaxed text-stone-700 shadow-sm">
                {children}
            </div>
        </section>
    );
}

function Step({ icon, title, children }) {
    return (
        <div className="flex gap-3">
            <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent-50 text-accent-700">
                <Icon name={icon} className="h-4 w-4" />
            </span>
            <div className="min-w-0">
                <p className="font-medium text-stone-900">{title}</p>
                <p className="mt-0.5 text-stone-600">{children}</p>
            </div>
        </div>
    );
}

export default function Help() {
    const header = (
        <header className="shrink-0 border-b border-stone-200 bg-white px-4 py-3">
            <p className="text-sm font-semibold text-stone-900">How this works</p>
            <p className="text-xs text-stone-500">Karachi civic reporting</p>
        </header>
    );

    return (
        <AppLayout header={header}>
            <Head title="How this works" />

            <Section title="What this does">
                <Step icon="chat" title="Describe the problem">
                    In Urdu, English, or a mix of both. Type it, speak it, or send a photo. You do not
                    need to know which department is responsible.
                </Step>
                <Step icon="map-pin" title="We work out who owns it">
                    We match what you describe against 41 Karachi authorities and 123 known places
                    across 28 towns, then explain the reasoning rather than just naming an office.
                </Step>
                <Step icon="envelope" title="We write the complaint">
                    You get a finished complaint in English and Urdu, addressed to the right authority,
                    ready to send on WhatsApp or email.
                </Step>
            </Section>

            <Section title="Why this is harder than it sounds">
                <p>
                    In Karachi the same street can belong to a cantonment board, a DHA scheme, a town
                    municipal corporation, or the water corporation, depending on exactly where you are
                    standing. Sending a complaint to the wrong one usually means it disappears.
                </p>
                <p>
                    The traps are real. DHA City is a completely separate scheme from DHA Phases 1 to 8,
                    and a complaint about one should never go to the other. Where jurisdiction is
                    genuinely disputed we say so and flag it for a person to check, instead of guessing
                    confidently and wasting your time.
                </p>
            </Section>

            <Section title="What happens after you send">
                <p>
                    We draft the complaint and hand it to you. We do not file it on your behalf, and we
                    do not have a back channel into any government department. You send it yourself, from
                    your own number or email, so the complaint comes from a real resident.
                </p>
                <p>
                    That is deliberate. A complaint from you carries weight that one from an intermediary
                    does not.
                </p>
            </Section>

            <Section title="Where we fall short">
                <p>
                    Of the 41 authorities we route to, 23 publish no working phone number or email at all.
                    Where that is true we say so plainly and point you at an escalation route, rather than
                    printing a contact that does not answer.
                </p>
                <p>
                    Voice notes need Chrome or Edge, including on Android. On an iPhone, use your
                    keyboard&apos;s own microphone key instead. It reaches the same place.
                </p>
            </Section>

            <div className="mt-6 mb-2">
                <Link
                    href="/"
                    className="flex min-h-12 items-center justify-center gap-2 rounded-2xl bg-accent-600 px-5 text-sm font-semibold text-white transition hover:bg-accent-700"
                >
                    <Icon name="chat" className="h-4 w-4" />
                    Report a problem
                </Link>
            </div>
        </AppLayout>
    );
}
