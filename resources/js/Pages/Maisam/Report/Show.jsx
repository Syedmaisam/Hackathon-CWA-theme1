import { useForm, Head, Link } from '@inertiajs/react';
import { useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

const CONFIDENCE_STYLE = {
    high: 'bg-emerald-100 text-emerald-800',
    medium: 'bg-amber-100 text-amber-800',
    low: 'bg-red-100 text-red-800',
    needs_human_review: 'bg-red-100 text-red-800',
};

const ROLE_LABEL = {
    primary: 'Primary',
    co_recipient: 'Co-recipient',
    escalation: 'Escalation',
};

const ISSUE_LABEL = {
    garbage: 'Garbage',
    construction_debris: 'Construction debris',
    sewer_overflow: 'Sewer overflow',
    water_supply: 'Water supply',
    tanker: 'Water tanker',
    road_damage: 'Road damage',
    streetlight: 'Streetlight',
    electrical_hazard: 'Electrical hazard',
    encroachment: 'Encroachment',
    illegal_construction: 'Illegal construction',
    storm_drain: 'Storm drain',
    park_amenity: 'Park / amenity',
    unknown: 'Unclassified',
};

function isUrdu(text) {
    return /[؀-ۿ]/.test(text ?? '');
}

function Chip({ children }) {
    return (
        <span className="rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-700">{children}</span>
    );
}

function ClarifyForm({ report }) {
    const { data, setData, post, processing, errors } = useForm({ answer: '' });

    function submit(e) {
        e.preventDefault();
        post(`/reports/${report.id}/clarify`);
    }

    return (
        <div className="rounded-lg border border-amber-200 bg-amber-50 p-4 sm:p-6">
            <p className="text-sm font-medium text-amber-900">
                {report.clarifying_question || 'We need a bit more detail before we can route this.'}
            </p>
            <form onSubmit={submit} className="mt-4 flex flex-col gap-3 sm:flex-row">
                <input
                    type="text"
                    value={data.answer}
                    onChange={(e) => setData('answer', e.target.value)}
                    placeholder="Type your answer…"
                    dir={/[؀-ۿ]/.test(data.answer) ? 'rtl' : 'ltr'}
                    className="min-h-11 flex-1 rounded-lg border border-stone-300 p-3 text-base focus:border-accent-600 focus:outline-none focus:ring-1 focus:ring-accent-600"
                />
                <button
                    type="submit"
                    disabled={processing || !data.answer.trim()}
                    className="min-h-11 rounded-lg bg-accent-600 px-5 py-3 text-sm font-medium text-white hover:bg-accent-700 disabled:opacity-40"
                >
                    Continue
                </button>
            </form>
            {errors.answer && <p className="mt-2 text-sm text-red-600">{errors.answer}</p>}
        </div>
    );
}

function CategoryPicker({ report, issueTypes }) {
    const { data, setData, post, processing } = useForm({ issue_type: '' });

    function submit(e) {
        e.preventDefault();
        post(`/reports/${report.id}/confirm-category`);
    }

    return (
        <div className="rounded-lg border border-red-200 bg-red-50 p-4 sm:p-6">
            <p className="text-sm font-medium text-red-900">
                We couldn't reach our classifier just now. Pick the closest category and we'll still route
                your complaint and draft it for you.
            </p>
            <form onSubmit={submit} className="mt-4 flex flex-col gap-3 sm:flex-row sm:flex-wrap">
                <select
                    value={data.issue_type}
                    onChange={(e) => setData('issue_type', e.target.value)}
                    className="min-h-11 rounded-lg border border-stone-300 bg-white p-3 text-base focus:border-accent-600 focus:outline-none focus:ring-1 focus:ring-accent-600"
                >
                    <option value="">Choose a category…</option>
                    {issueTypes.map((type) => (
                        <option key={type} value={type}>
                            {ISSUE_LABEL[type] ?? type}
                        </option>
                    ))}
                </select>
                <button
                    type="submit"
                    disabled={processing || !data.issue_type}
                    className="min-h-11 rounded-lg bg-accent-600 px-5 py-3 text-sm font-medium text-white hover:bg-accent-700 disabled:opacity-40"
                >
                    Route my complaint
                </button>
            </form>
        </div>
    );
}

export default function Show({ report, routing, issueTypes }) {
    const [locale, setLocale] = useState('en');
    const [copied, setCopied] = useState(false);

    const draft = locale === 'en' ? report.draft_en : report.draft_ur;
    const hasOverride = report.routing_flags?.includes('special_zone_override');
    const hasUnverified = routing.some((r) => r.contact_unverified);
    const verifiedEmail = routing.find((r) => r.email)?.email;

    function copyDraft() {
        navigator.clipboard.writeText(draft ?? '');
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    }

    return (
        <AppLayout>
            <Head title="Your report" />

            {/* The report reads as the message you sent, so the routing below it
                reads as the reply. Same shape as the compose screen. */}
            <div className="space-y-3">
                <div className="flex justify-end">
                    <div className="max-w-[85%] space-y-2 sm:max-w-[75%]">
                        {report.photo_url && (
                            <img
                                src={report.photo_url}
                                alt="Attached to report"
                                className="w-full rounded-2xl rounded-br-sm object-cover"
                            />
                        )}
                        <div
                            dir={isUrdu(report.raw_text) ? 'rtl' : 'ltr'}
                            className="rounded-2xl rounded-br-sm bg-accent-600 px-4 py-3 text-sm whitespace-pre-wrap text-white shadow-sm"
                        >
                            {report.raw_text}
                        </div>
                    </div>
                </div>

                <div className="flex flex-wrap items-center justify-end gap-2">
                    {report.issue_type && <Chip>{ISSUE_LABEL[report.issue_type] ?? report.issue_type}</Chip>}
                    {report.severity && <Chip>Severity: {report.severity}</Chip>}
                    {(report.hazards ?? []).map((hazard) => (
                        <Chip key={hazard}>{hazard.replaceAll('_', ' ')}</Chip>
                    ))}
                    {report.resolved_area && <Chip>📍 {report.resolved_area}</Chip>}
                    {report.input_mode === 'voice' && <Chip>🎙 voice</Chip>}
                </div>
            </div>

            <div className="mt-8">
                {report.status === 'awaiting_answer' && <ClarifyForm report={report} />}

                {report.status === 'ai_failed' && <CategoryPicker report={report} issueTypes={issueTypes} />}

                {report.status === 'needs_review' && (
                    <div className="rounded-xl border border-amber-200 bg-amber-50 p-4 sm:p-6">
                        <div className="flex items-start gap-3">
                            <span className="text-xl" aria-hidden="true">
                                🔍
                            </span>
                            <div className="text-sm text-amber-900">
                                <p className="font-medium">A person needs to check this one</p>
                                <p className="mt-1">
                                    Jurisdiction here is genuinely disputed, so we would rather say so than
                                    guess and send your complaint to the wrong office. DHA City, for example,
                                    is a separate scheme from DHA Phases 1 to 8. We have logged it, and
                                    suggest filing through the{' '}
                                    <span className="font-medium">Pakistan Citizen Portal</span> meanwhile.
                                </p>
                            </div>
                        </div>
                    </div>
                )}

                {report.status === 'drafted' && (
                    <>
                        {/* The whole product promise in one line: we worked out who
                            owns this. Without it the page opens on two grey cards. */}
                        <div className="mb-6 flex items-start gap-3 rounded-xl border border-accent-100 bg-accent-50 p-4">
                            <span className="text-xl" aria-hidden="true">
                                ✅
                            </span>
                            <div>
                                <p className="font-medium text-stone-900">
                                    Routed to {routing[0]?.name ?? 'the responsible authority'}
                                </p>
                                <p className="mt-0.5 text-sm text-stone-600">
                                    Your complaint is written and ready to send. Review it below, then send it
                                    on WhatsApp or email.
                                </p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <section className="rounded-lg border border-stone-200 bg-white p-4 sm:p-6">
                            <div className="flex flex-wrap items-center justify-between gap-2">
                                <h2 className="text-sm font-semibold uppercase tracking-wide text-stone-500">
                                    Who this goes to
                                </h2>
                                {report.routing_confidence && (
                                    <span
                                        className={`rounded-full px-3 py-1 text-xs font-medium ${
                                            CONFIDENCE_STYLE[report.routing_confidence] ?? 'bg-stone-100 text-stone-700'
                                        }`}
                                    >
                                        {report.routing_confidence} confidence
                                    </span>
                                )}
                            </div>

                            {hasOverride && (
                                <p className="mt-3 rounded-lg bg-accent-50 p-3 text-sm text-accent-800">
                                    Inside a cantonment / DHA special zone — this overrides the usual district
                                    routing.
                                </p>
                            )}

                            <ul className="mt-4 space-y-4">
                                {routing.map((r, i) => (
                                    <li key={i} className="border-t border-stone-100 pt-4 first:border-0 first:pt-0">
                                        <div className="flex flex-wrap items-center gap-2">
                                            <span className="font-medium text-stone-900">{r.name}</span>
                                            <span className="rounded-full bg-stone-100 px-2 py-0.5 text-xs text-stone-600">
                                                {ROLE_LABEL[r.role] ?? r.role}
                                            </span>
                                        </div>
                                        <p className="mt-1 text-sm text-stone-500">{r.reason}</p>
                                        {r.contact_unverified ? (
                                            <p className="mt-1 text-xs font-medium text-red-600">
                                                No verified public contact on file for this authority.
                                            </p>
                                        ) : !r.phone && !r.email && !r.website ? (
                                            /* 23 of the 41 seeded authorities have no phone or email.
                                               Without this the card renders a silent blank gap, which
                                               reads as a broken page rather than missing public data. */
                                            <p className="mt-1 text-xs text-stone-500">
                                                No public contact channel published. Send via the escalation
                                                route below.
                                            </p>
                                        ) : (
                                            /* Stacked, not a dot-separated run: authority emails and
                                               URLs here are long enough to overflow a phone, and a
                                               tappable phone number is the whole point on mobile. */
                                            <div className="mt-2 space-y-1 text-sm">
                                                {r.phone && (
                                                    <a
                                                        href={`tel:${r.phone.replace(/[^+\d]/g, '')}`}
                                                        className="flex min-h-8 items-center gap-2 text-accent-700 hover:underline"
                                                    >
                                                        <span aria-hidden="true">☎</span>
                                                        <span className="break-all">{r.phone}</span>
                                                    </a>
                                                )}
                                                {r.email && (
                                                    <a
                                                        href={`mailto:${r.email}`}
                                                        className="flex min-h-8 items-center gap-2 text-accent-700 hover:underline"
                                                    >
                                                        <span aria-hidden="true">✉</span>
                                                        <span className="break-all">{r.email}</span>
                                                    </a>
                                                )}
                                                {r.website && (
                                                    <a
                                                        href={r.website}
                                                        target="_blank"
                                                        rel="noreferrer"
                                                        className="flex min-h-8 items-center gap-2 text-stone-600 hover:underline"
                                                    >
                                                        <span aria-hidden="true">🔗</span>
                                                        <span className="break-all">{r.website}</span>
                                                    </a>
                                                )}
                                            </div>
                                        )}
                                    </li>
                                ))}
                            </ul>

                            {hasUnverified && (
                                <p className="mt-4 text-xs text-stone-500">
                                    Contacts marked unverified aren't confirmed live — we still route to them,
                                    but recommend also using a verified channel above where one exists.
                                </p>
                            )}
                        </section>

                        <section className="rounded-lg border border-stone-200 bg-white p-4 sm:p-6">
                            <div className="flex flex-wrap items-center justify-between gap-3">
                                <h2 className="text-sm font-semibold uppercase tracking-wide text-stone-500">
                                    Complaint draft
                                </h2>
                                <div className="flex overflow-hidden rounded-lg border border-stone-200 text-sm">
                                    <button
                                        type="button"
                                        onClick={() => setLocale('en')}
                                        aria-pressed={locale === 'en'}
                                        className={`min-h-9 px-4 transition ${locale === 'en' ? 'bg-accent-600 text-white' : 'bg-white text-stone-600 hover:bg-stone-50'}`}
                                    >
                                        English
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setLocale('ur')}
                                        aria-pressed={locale === 'ur'}
                                        className={`min-h-9 px-4 transition ${locale === 'ur' ? 'bg-accent-600 text-white' : 'bg-white text-stone-600 hover:bg-stone-50'}`}
                                    >
                                        اردو
                                    </button>
                                </div>
                            </div>

                            <pre
                                dir={locale === 'ur' ? 'rtl' : 'ltr'}
                                className="mt-4 max-h-80 overflow-y-auto rounded-lg bg-stone-50 p-4 font-sans text-sm break-words whitespace-pre-wrap text-stone-800 sm:max-h-96"
                            >
                                {draft}
                            </pre>

                            {/* WhatsApp first and full-width on mobile: it is how a Karachi
                                resident actually sends this, and the other two are secondary. */}
                            <div className="mt-4 grid grid-cols-1 gap-2 sm:flex sm:flex-wrap">
                                <a
                                    href={`https://wa.me/?text=${encodeURIComponent(draft ?? '')}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="flex min-h-11 items-center justify-center rounded-lg bg-emerald-600 px-4 text-sm font-medium text-white transition hover:bg-emerald-700"
                                >
                                    Send on WhatsApp
                                </a>
                                <button
                                    type="button"
                                    onClick={copyDraft}
                                    className="flex min-h-11 items-center justify-center rounded-lg border border-stone-300 px-4 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                                >
                                    {copied ? 'Copied ✓' : 'Copy'}
                                </button>
                                {verifiedEmail && (
                                    <a
                                        href={`mailto:${verifiedEmail}?subject=${encodeURIComponent(
                                            ISSUE_LABEL[report.issue_type] ?? 'Civic complaint',
                                        )}&body=${encodeURIComponent(draft ?? '')}`}
                                        className="flex min-h-11 items-center justify-center rounded-lg border border-stone-300 px-4 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                                    >
                                        Email
                                    </a>
                                )}
                            </div>
                            </section>
                        </div>

                        <div className="mt-6 text-center">
                            <Link
                                href="/"
                                className="inline-flex min-h-11 items-center rounded-lg border border-stone-300 bg-white px-5 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                            >
                                Report something else
                            </Link>
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
