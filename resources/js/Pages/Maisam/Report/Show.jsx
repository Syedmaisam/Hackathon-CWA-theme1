import { useForm, Head } from '@inertiajs/react';
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
        <div className="rounded-lg border border-amber-200 bg-amber-50 p-6">
            <p className="text-sm font-medium text-amber-900">
                {report.clarifying_question || 'We need a bit more detail before we can route this.'}
            </p>
            <form onSubmit={submit} className="mt-4 flex gap-3">
                <input
                    type="text"
                    value={data.answer}
                    onChange={(e) => setData('answer', e.target.value)}
                    placeholder="Type your answer…"
                    className="flex-1 rounded-lg border border-stone-300 p-3 text-sm focus:border-accent-600 focus:outline-none focus:ring-1 focus:ring-accent-600"
                />
                <button
                    type="submit"
                    disabled={processing || !data.answer.trim()}
                    className="rounded-lg bg-accent-600 px-5 py-3 text-sm font-medium text-white hover:bg-accent-700 disabled:opacity-40"
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
        <div className="rounded-lg border border-red-200 bg-red-50 p-6">
            <p className="text-sm font-medium text-red-900">
                We couldn't reach our classifier just now. Pick the closest category and we'll still route
                your complaint and draft it for you.
            </p>
            <form onSubmit={submit} className="mt-4 flex flex-wrap gap-3">
                <select
                    value={data.issue_type}
                    onChange={(e) => setData('issue_type', e.target.value)}
                    className="rounded-lg border border-stone-300 p-3 text-sm focus:border-accent-600 focus:outline-none focus:ring-1 focus:ring-accent-600"
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
                    className="rounded-lg bg-accent-600 px-5 py-3 text-sm font-medium text-white hover:bg-accent-700 disabled:opacity-40"
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

            <div className="space-y-3">
                <div className="flex flex-wrap items-center gap-2">
                    {report.issue_type && <Chip>{ISSUE_LABEL[report.issue_type] ?? report.issue_type}</Chip>}
                    {report.severity && <Chip>Severity: {report.severity}</Chip>}
                    {(report.hazards ?? []).map((hazard) => (
                        <Chip key={hazard}>{hazard.replaceAll('_', ' ')}</Chip>
                    ))}
                    {report.resolved_area && <Chip>📍 {report.resolved_area}</Chip>}
                    {report.input_mode === 'voice' && <Chip>🎙 voice</Chip>}
                </div>

                <p className="text-stone-700">{report.raw_text}</p>

                {report.photo_url && (
                    <img src={report.photo_url} alt="Attached to report" className="max-h-64 rounded-lg" />
                )}
            </div>

            <div className="mt-8">
                {report.status === 'awaiting_answer' && <ClarifyForm report={report} />}

                {report.status === 'ai_failed' && <CategoryPicker report={report} issueTypes={issueTypes} />}

                {report.status === 'needs_review' && (
                    <div className="rounded-lg border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900">
                        This location needs a human to confirm jurisdiction before we route it automatically
                        (for example, DHA City is a separate scheme from DHA Phases 1-8). We've logged it and
                        suggest filing through the{' '}
                        <span className="font-medium">Pakistan Citizen Portal</span> in the meantime.
                    </div>
                )}

                {report.status === 'drafted' && (
                    <div className="grid grid-cols-1 gap-6 lg:grid-cols-2">
                        <section className="rounded-lg border border-stone-200 p-6">
                            <div className="flex items-center justify-between">
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
                                        <div className="flex items-center gap-2">
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
                                        ) : (
                                            <p className="mt-1 text-sm text-stone-700">
                                                {r.phone && <span>☎ {r.phone}</span>}
                                                {r.phone && (r.email || r.website) && ' · '}
                                                {r.email && <span>✉ {r.email}</span>}
                                                {r.email && r.website && ' · '}
                                                {r.website && <span>{r.website}</span>}
                                            </p>
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

                        <section className="rounded-lg border border-stone-200 p-6">
                            <div className="flex items-center justify-between">
                                <h2 className="text-sm font-semibold uppercase tracking-wide text-stone-500">
                                    Complaint draft
                                </h2>
                                <div className="flex overflow-hidden rounded-lg border border-stone-200 text-sm">
                                    <button
                                        onClick={() => setLocale('en')}
                                        className={`px-3 py-1 ${locale === 'en' ? 'bg-accent-600 text-white' : 'bg-white text-stone-600'}`}
                                    >
                                        English
                                    </button>
                                    <button
                                        onClick={() => setLocale('ur')}
                                        className={`px-3 py-1 ${locale === 'ur' ? 'bg-accent-600 text-white' : 'bg-white text-stone-600'}`}
                                    >
                                        اردو
                                    </button>
                                </div>
                            </div>

                            <pre
                                dir={locale === 'ur' ? 'rtl' : 'ltr'}
                                className="mt-4 max-h-96 overflow-y-auto whitespace-pre-wrap font-sans text-sm text-stone-800"
                            >
                                {draft}
                            </pre>

                            <div className="mt-4 flex flex-wrap gap-2">
                                <button
                                    onClick={copyDraft}
                                    className="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50"
                                >
                                    {copied ? 'Copied ✓' : 'Copy'}
                                </button>
                                <a
                                    href={`https://wa.me/?text=${encodeURIComponent(draft ?? '')}`}
                                    target="_blank"
                                    rel="noreferrer"
                                    className="rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white hover:bg-emerald-700"
                                >
                                    WhatsApp
                                </a>
                                {verifiedEmail && (
                                    <a
                                        href={`mailto:${verifiedEmail}?subject=${encodeURIComponent(
                                            ISSUE_LABEL[report.issue_type] ?? 'Civic complaint',
                                        )}&body=${encodeURIComponent(draft ?? '')}`}
                                        className="rounded-lg border border-stone-300 px-4 py-2 text-sm font-medium text-stone-700 hover:bg-stone-50"
                                    >
                                        Email
                                    </a>
                                )}
                            </div>
                        </section>
                    </div>
                )}
            </div>
        </AppLayout>
    );
}
