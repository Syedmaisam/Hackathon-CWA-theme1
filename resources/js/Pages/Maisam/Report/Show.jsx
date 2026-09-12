import { useForm, Head, Link, usePoll } from '@inertiajs/react';
import { useEffect, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import Icon from '@/Components/Icon';

// Every status the page knows how to render. Anything else — `pending`, or a
// status added later — is treated as "still being worked on" rather than
// rendering an empty page under the chips.
const SETTLED_STATUSES = ['awaiting_answer', 'ai_failed', 'needs_review', 'drafted'];

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

function Chip({ icon, children }) {
    return (
        <span className="inline-flex items-center gap-1 rounded-full bg-stone-100 px-3 py-1 text-xs font-medium text-stone-700">
            {icon && <Icon name={icon} className="h-3.5 w-3.5" />}
            {children}
        </span>
    );
}

/** A grouped section, the way an app lists settings — label, then one surface. */
function Section({ title, action, children }) {
    return (
        <section className="mt-6">
            <div className="flex items-end justify-between gap-2 px-1 pb-2">
                <h2 className="text-xs font-semibold tracking-wide text-stone-500 uppercase">{title}</h2>
                {action}
            </div>
            <div className="overflow-hidden rounded-2xl bg-white shadow-sm">{children}</div>
        </section>
    );
}

/** The citizen's follow-up, shown as an outgoing bubble while it is being processed. */
function SentByYou({ children }) {
    return (
        <div className="flex justify-end">
            <div
                dir={isUrdu(children) ? 'rtl' : 'ltr'}
                className="max-w-[85%] rounded-2xl rounded-br-sm bg-accent-600 px-4 py-3 text-sm whitespace-pre-wrap text-white shadow-sm sm:max-w-[75%]"
            >
                {children}
            </div>
        </div>
    );
}

function ClarifyForm({ report }) {
    const { data, setData, post, processing, errors } = useForm({ answer: '' });

    function submit(e) {
        e.preventDefault();
        post(`/reports/${report.id}/clarify`);
    }

    // The clarify round trip re-runs the whole pipeline, including a live
    // model call, so the wait is seconds rather than milliseconds.
    if (processing) {
        return (
            <div className="space-y-3">
                <SentByYou>{data.answer}</SentByYou>
                <StillProcessing
                    caption="Thanks — routing it now…"
                    detail="We're re-reading your report with that detail, finding who owns this, and writing the complaint."
                />
            </div>
        );
    }

    return (
        <div className="rounded-2xl border border-amber-200 bg-amber-50 p-4">
            <p className="text-sm font-medium text-amber-900">
                {report.clarifying_question || 'We need a bit more detail before we can route this.'}
            </p>
            <form onSubmit={submit} className="mt-3 space-y-2">
                <input
                    type="text"
                    value={data.answer}
                    onChange={(e) => setData('answer', e.target.value)}
                    placeholder="Type your answer…"
                    disabled={processing}
                    dir={isUrdu(data.answer) ? 'rtl' : 'ltr'}
                    className="min-h-11 w-full rounded-xl border border-amber-200 bg-white p-3 text-base focus:border-accent-600 focus:ring-1 focus:ring-accent-600 focus:outline-none disabled:opacity-60"
                />
                <button
                    type="submit"
                    disabled={processing || !data.answer.trim()}
                    className="flex min-h-11 w-full items-center justify-center gap-2 rounded-xl bg-accent-600 px-5 text-sm font-semibold text-white transition hover:bg-accent-700 disabled:opacity-40"
                >
                    {processing ? 'Working it out…' : 'Continue'}
                </button>
            </form>
            {errors.answer && <p className="mt-2 text-sm text-red-600">{errors.answer}</p>}
        </div>
    );
}

/**
 * The compose screen's typing indicator, as the reply bubble on this screen.
 * Shown while the pipeline is still running so a refresh mid-call, an async
 * queue, or a crash between save and catch never lands on a dead page.
 */
function StillProcessing({
    caption = 'Still working on your report…',
    detail = "We're reading it, finding who owns this, and writing the complaint. This page updates by itself — no need to refresh.",
}) {
    return (
        <div className="flex">
            <div className="max-w-[85%] rounded-2xl rounded-tl-sm bg-white px-4 py-3 text-sm text-stone-800 shadow-sm sm:max-w-[75%]">
                <div className="flex items-center gap-2">
                    {[0, 1, 2].map((dot) => (
                        <span
                            key={dot}
                            className="h-2 w-2 rounded-full bg-stone-400"
                            style={{ animation: `pulse 1.2s ease-in-out ${dot * 0.2}s infinite` }}
                        />
                    ))}
                    <span className="ml-1 text-stone-600">{caption}</span>
                </div>
                <p className="mt-2 text-stone-500">{detail}</p>
            </div>
        </div>
    );
}

function CategoryPicker({ report, issueTypes }) {
    const { data, setData, post, processing } = useForm({ issue_type: '' });

    function submit(e) {
        e.preventDefault();
        post(`/reports/${report.id}/confirm-category`);
    }

    if (processing) {
        return (
            <div className="space-y-3">
                <SentByYou>{ISSUE_LABEL[data.issue_type] ?? data.issue_type}</SentByYou>
                <StillProcessing
                    caption="Routing your complaint…"
                    detail="Finding who owns this and writing the complaint from your chosen category."
                />
            </div>
        );
    }

    return (
        <div className="rounded-2xl border border-red-200 bg-red-50 p-4">
            <p className="text-sm font-medium text-red-900">
                We couldn&apos;t reach our classifier just now. Pick the closest category and we&apos;ll
                still route your complaint and draft it for you.
            </p>
            <form onSubmit={submit} className="mt-3 space-y-2">
                <select
                    value={data.issue_type}
                    onChange={(e) => setData('issue_type', e.target.value)}
                    disabled={processing}
                    className="min-h-11 w-full rounded-xl border border-red-200 bg-white p-3 text-base focus:border-accent-600 focus:ring-1 focus:ring-accent-600 focus:outline-none disabled:opacity-60"
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
                    className="flex min-h-11 w-full items-center justify-center rounded-xl bg-accent-600 px-5 text-sm font-semibold text-white transition hover:bg-accent-700 disabled:opacity-40"
                >
                    {processing ? 'Routing your complaint…' : 'Route my complaint'}
                </button>
            </form>
        </div>
    );
}

export default function Show({ report, routing, issueTypes }) {
    const [locale, setLocale] = useState('en');
    const [copied, setCopied] = useState(false);

    const draft = (locale === 'en' ? report.draft_en : report.draft_ur)?.trim() || null;
    const hasOverride = report.routing_flags?.includes('special_zone_override');
    const hasUnverified = routing.some((r) => r.contact_unverified);
    const verifiedEmail = routing.find((r) => r.email)?.email;
    const isProcessing = !SETTLED_STATUSES.includes(report.status);

    // Re-fetch the report while it is still pending so the page advances to
    // the routed result on its own. Stops the moment the status settles.
    const { stop: stopPolling } = usePoll(3000, { only: ['report', 'routing'] }, { autoStart: isProcessing });

    useEffect(() => {
        if (!isProcessing) {
            stopPolling();
        }
    }, [isProcessing, stopPolling]);

    function copyDraft() {
        if (!draft) {
            return;
        }

        navigator.clipboard.writeText(draft);
        setCopied(true);
        setTimeout(() => setCopied(false), 2000);
    }

    const header = (
        <header className="flex shrink-0 items-center gap-2 border-b border-stone-200 bg-white px-2 py-3">
            <Link
                href="/"
                aria-label="Back to reporting"
                className="flex h-10 w-10 shrink-0 items-center justify-center rounded-full text-stone-600 transition hover:bg-stone-100"
            >
                <Icon name="arrow-left" className="h-5 w-5" />
            </Link>
            <div className="min-w-0">
                <p className="truncate text-sm font-semibold text-stone-900">Your report</p>
                <p className="truncate text-xs text-stone-500">
                    {report.resolved_area ? `${report.resolved_area}, Karachi` : 'Karachi civic reporting'}
                </p>
            </div>
        </header>
    );

    // Sending is the entire point of this screen, so on a phone it is pinned
    // above the tab bar rather than sitting below a long scroll.
    const actions = report.status === 'drafted' && draft && (
        <div className="shrink-0 border-t border-stone-200 bg-white px-4 py-3">
            <div className="mx-auto flex max-w-2xl gap-2">
                <a
                    href={`https://wa.me/?text=${encodeURIComponent(draft)}`}
                    target="_blank"
                    rel="noreferrer"
                    className="flex min-h-12 flex-1 items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 text-sm font-semibold text-white transition hover:bg-emerald-700"
                >
                    <Icon name="send" className="h-4 w-4" />
                    Send on WhatsApp
                </a>
                <button
                    type="button"
                    onClick={copyDraft}
                    aria-label="Copy the complaint"
                    className="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-stone-300 text-stone-600 transition hover:bg-stone-50"
                >
                    <Icon name={copied ? 'check' : 'clipboard'} className="h-5 w-5" />
                </button>
                {verifiedEmail && (
                    <a
                        href={`mailto:${verifiedEmail}?subject=${encodeURIComponent(
                            ISSUE_LABEL[report.issue_type] ?? 'Civic complaint',
                        )}&body=${encodeURIComponent(draft)}`}
                        aria-label="Send by email"
                        className="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl border border-stone-300 text-stone-600 transition hover:bg-stone-50"
                    >
                        <Icon name="envelope" className="h-5 w-5" />
                    </a>
                )}
            </div>
        </div>
    );

    return (
        <AppLayout header={header} footer={actions}>
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
                    {report.resolved_area && <Chip icon="map-pin">{report.resolved_area}</Chip>}
                    {report.input_mode === 'voice' && <Chip icon="microphone">voice</Chip>}
                </div>
            </div>

            <div className="mt-6">
                {isProcessing && <StillProcessing />}

                {report.status === 'awaiting_answer' && <ClarifyForm report={report} />}

                {report.status === 'ai_failed' && <CategoryPicker report={report} issueTypes={issueTypes} />}

                {report.status === 'needs_review' && (
                    <div className="rounded-2xl border border-amber-200 bg-amber-50 p-4">
                        <div className="flex items-start gap-3">
                            <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                                <Icon name="magnifying-glass" className="h-4 w-4" />
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
                        <div className="flex items-start gap-3 rounded-2xl border border-accent-100 bg-accent-50 p-4">
                            <span className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-accent-600 text-white">
                                <Icon name="check" className="h-4 w-4" strokeWidth={2.5} />
                            </span>
                            <div className="min-w-0">
                                <p className="font-medium text-stone-900">
                                    Routed to {routing[0]?.name ?? 'the responsible authority'}
                                </p>
                                <p className="mt-0.5 text-sm text-stone-600">
                                    Your complaint is written and ready to send.
                                </p>
                            </div>
                        </div>

                        <Section
                            title="Who this goes to"
                            action={
                                report.routing_confidence && (
                                    <span
                                        className={`rounded-full px-2.5 py-1 text-[11px] font-medium ${
                                            CONFIDENCE_STYLE[report.routing_confidence] ??
                                            'bg-stone-100 text-stone-700'
                                        }`}
                                    >
                                        {report.routing_confidence.replaceAll('_', ' ')} confidence
                                    </span>
                                )
                            }
                        >
                            {hasOverride && (
                                <p className="border-b border-stone-100 bg-accent-50 p-4 text-sm text-accent-800">
                                    Inside a cantonment / DHA special zone — this overrides the usual
                                    district routing.
                                </p>
                            )}

                            <ul className="divide-y divide-stone-100">
                                {routing.map((r, i) => (
                                    <li key={i} className="p-4">
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
                                                        <Icon name="phone" className="h-4 w-4 shrink-0" />
                                                        <span className="break-all">{r.phone}</span>
                                                    </a>
                                                )}
                                                {r.email && (
                                                    <a
                                                        href={`mailto:${r.email}`}
                                                        className="flex min-h-8 items-center gap-2 text-accent-700 hover:underline"
                                                    >
                                                        <Icon name="envelope" className="h-4 w-4 shrink-0" />
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
                                                        <Icon name="link" className="h-4 w-4 shrink-0" />
                                                        <span className="break-all">{r.website}</span>
                                                    </a>
                                                )}
                                            </div>
                                        )}
                                    </li>
                                ))}
                            </ul>

                            {hasUnverified && (
                                <p className="border-t border-stone-100 p-4 text-xs text-stone-500">
                                    Contacts marked unverified aren&apos;t confirmed live — we still route to
                                    them, but recommend also using a verified channel above where one exists.
                                </p>
                            )}
                        </Section>

                        <Section
                            title="Complaint draft"
                            action={
                                <div className="flex overflow-hidden rounded-full border border-stone-200 text-xs">
                                    <button
                                        type="button"
                                        onClick={() => setLocale('en')}
                                        aria-pressed={locale === 'en'}
                                        className={`min-h-8 px-3 transition ${locale === 'en' ? 'bg-accent-600 text-white' : 'bg-white text-stone-600'}`}
                                    >
                                        English
                                    </button>
                                    <button
                                        type="button"
                                        onClick={() => setLocale('ur')}
                                        aria-pressed={locale === 'ur'}
                                        className={`min-h-8 px-3 transition ${locale === 'ur' ? 'bg-accent-600 text-white' : 'bg-white text-stone-600'}`}
                                    >
                                        اردو
                                    </button>
                                </div>
                            }
                        >
                            {draft ? (
                                <pre
                                    dir={locale === 'ur' ? 'rtl' : 'ltr'}
                                    className="max-h-96 overflow-y-auto p-4 font-sans text-sm break-words whitespace-pre-wrap text-stone-800"
                                >
                                    {draft}
                                </pre>
                            ) : (
                                /* A drafted report can still carry a null draft in one
                                   language. Say so rather than offering to send nothing.
                                   The send actions in the footer hide in the same case. */
                                <p className="p-4 text-sm text-stone-500">
                                    {locale === 'ur'
                                        ? 'The Urdu version of this complaint is not available. Switch to English to send it.'
                                        : 'The English version of this complaint is not available. Switch to Urdu to send it.'}
                                </p>
                            )}
                        </Section>

                        <div className="mt-6 mb-2">
                            <Link
                                href="/"
                                className="flex min-h-11 items-center justify-center gap-2 rounded-2xl border border-stone-300 bg-white px-5 text-sm font-medium text-stone-700 transition hover:bg-stone-50"
                            >
                                <Icon name="plus-square" className="h-4 w-4" />
                                Report something else
                            </Link>
                        </div>
                    </>
                )}
            </div>
        </AppLayout>
    );
}
