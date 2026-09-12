import { useForm, Head, Link } from '@inertiajs/react';
import { useEffect, useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';
import PlacePicker, { placeLabel } from '@/Components/PlacePicker';
import VoiceNoteInput from '@/Components/VoiceNoteInput';

const STEPS = ['Reading your report', 'Finding who owns this', 'Writing the complaint'];

const PROMPTS = [
    'Sewage overflowing on our street',
    'Garbage not collected for days',
    'Streetlight out on the main road',
    'Water tanker never arrived',
];

function isUrdu(text) {
    return /[؀-ۿ]/.test(text ?? '');
}

/** An incoming bubble, as the service talking to the citizen. */
function SaidToYou({ children }) {
    return (
        <div className="flex">
            <div className="max-w-[85%] rounded-2xl rounded-tl-sm bg-white px-4 py-3 text-sm text-stone-800 shadow-sm sm:max-w-[75%]">
                {children}
            </div>
        </div>
    );
}

export default function Create({ places }) {
    const { data, setData, post, processing, errors } = useForm({
        raw_text: '',
        citizen_name: '',
        citizen_phone: '',
        input_mode: 'text',
        photo: null,
        gazetteer_node_id: null,
    });

    // The picked town/UC object, kept alongside the id the form posts so the
    // chip and the in-flight bubble can show its name.
    const [place, setPlace] = useState(null);
    const [photoPreview, setPhotoPreview] = useState(null);
    const [showContact, setShowContact] = useState(false);
    const [step, setStep] = useState(0);
    const fileInput = useRef(null);
    const textarea = useRef(null);
    const thread = useRef(null);

    // Advance the processing caption so the wait reads as progress rather than
    // a frozen screen. The pipeline runs inline, so this is a pace, not a probe.
    useEffect(() => {
        if (!processing) {
            setStep(0);

            return undefined;
        }

        const timer = setInterval(() => {
            setStep((current) => Math.min(current + 1, STEPS.length - 1));
        }, 1800);

        return () => clearInterval(timer);
    }, [processing]);

    useEffect(() => {
        thread.current?.scrollTo({ top: thread.current.scrollHeight, behavior: 'smooth' });
    }, [photoPreview, showContact, processing]);

    function pickPhoto(file) {
        if (!file) {
            return;
        }

        setData('photo', file);
        setPhotoPreview(URL.createObjectURL(file));
    }

    function clearPhoto() {
        setData('photo', null);
        setPhotoPreview(null);

        if (fileInput.current) {
            fileInput.current.value = '';
        }
    }

    // Transcript arrives one settled phrase at a time, so append rather than
    // replace. input_mode flips to voice on the first phrase and stays there
    // even if the citizen then edits the text by hand — the report did start
    // as a voice note, and the show page badges it as one.
    function appendTranscript(phrase) {
        setData((current) => ({
            ...current,
            raw_text: current.raw_text ? `${current.raw_text.trimEnd()} ${phrase}` : phrase,
            input_mode: 'voice',
        }));
    }

    function grow(element) {
        if (!element) {
            return;
        }

        element.style.height = 'auto';
        element.style.height = `${Math.min(element.scrollHeight, 160)}px`;
    }

    function pickPlace(next) {
        setPlace(next);
        setData('gazetteer_node_id', next?.id ?? null);
    }

    function submit(e) {
        e?.preventDefault();

        if (!data.raw_text.trim() || !data.gazetteer_node_id || processing) {
            return;
        }

        post('/reports', { forceFormData: true });
    }

    function onKeyDown(e) {
        // Enter sends on a desktop keyboard; Shift+Enter makes a new line. On a
        // phone the on-screen Enter key inserts a newline as people expect, so
        // this is deliberately desktop-only.
        if (e.key === 'Enter' && !e.shiftKey && window.matchMedia('(min-width: 640px)').matches) {
            e.preventDefault();
            submit();
        }
    }

    const canSend = data.raw_text.trim().length > 0 && data.gazetteer_node_id !== null && !processing;

    return (
        <AppLayout bare>
            <Head title="Report a problem" />

            <header className="flex shrink-0 items-center gap-3 border-b border-stone-200 bg-white px-4 py-3">
                <Link href="/" className="flex h-10 w-10 items-center justify-center rounded-full bg-accent-600 text-base text-white">
                    🏙
                </Link>
                <div className="min-w-0">
                    <p className="truncate text-sm font-semibold text-stone-900">The City Around You</p>
                    <p className="truncate text-xs text-stone-500">
                        {processing ? 'typing…' : 'Karachi civic reporting'}
                    </p>
                </div>
            </header>

            <div ref={thread} className="flex-1 overflow-y-auto px-4 py-4">
                <div className="mx-auto max-w-2xl space-y-3">
                    <SaidToYou>
                        <p className="font-medium text-stone-900">What&apos;s broken near you?</p>
                        <p className="mt-1 text-stone-600">
                            Pick your town or union council, then describe it in your own words — English,
                            Urdu, or a mix. Send a photo or a voice note if that&apos;s easier. We&apos;ll
                            work out who&apos;s responsible and write the complaint for you.
                        </p>
                    </SaidToYou>

                    {!data.raw_text && !processing && (
                        <div className="flex flex-wrap gap-2 pt-1">
                            {PROMPTS.map((prompt) => (
                                <button
                                    key={prompt}
                                    type="button"
                                    onClick={() => {
                                        setData('raw_text', prompt);
                                        textarea.current?.focus();
                                    }}
                                    className="rounded-full border border-stone-300 bg-white px-3 py-2 text-xs text-stone-600 transition hover:border-accent-600 hover:text-accent-700"
                                >
                                    {prompt}
                                </button>
                            ))}
                        </div>
                    )}

                    {photoPreview && (
                        <div className="flex justify-end">
                            <div className="relative max-w-[70%] overflow-hidden rounded-2xl rounded-br-sm bg-accent-600 p-1 shadow-sm">
                                <img src={photoPreview} alt="Attached to your report" className="rounded-xl" />
                                <button
                                    type="button"
                                    onClick={clearPhoto}
                                    aria-label="Remove photo"
                                    className="absolute top-3 right-3 flex h-8 w-8 items-center justify-center rounded-full bg-stone-900/60 text-sm text-white"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>
                    )}

                    {processing && (
                        <>
                            <div className="flex justify-end">
                                <div
                                    dir={isUrdu(data.raw_text) ? 'rtl' : 'ltr'}
                                    className="max-w-[85%] rounded-2xl rounded-br-sm bg-accent-600 px-4 py-3 text-sm whitespace-pre-wrap text-white shadow-sm sm:max-w-[75%]"
                                >
                                    {data.raw_text}
                                    {place && (
                                        <span dir="ltr" className="mt-2 block text-xs text-white/80">
                                            📍 {placeLabel(place)}
                                        </span>
                                    )}
                                </div>
                            </div>

                            <SaidToYou>
                                <div className="flex items-center gap-2">
                                    {[0, 1, 2].map((dot) => (
                                        <span
                                            key={dot}
                                            className="h-2 w-2 rounded-full bg-stone-400"
                                            style={{ animation: `pulse 1.2s ease-in-out ${dot * 0.2}s infinite` }}
                                        />
                                    ))}
                                    <span className="ml-1 text-stone-600">{STEPS[step]}…</span>
                                </div>
                            </SaidToYou>
                        </>
                    )}

                    {(errors.raw_text || errors.gazetteer_node_id) && (
                        <p className="text-center text-sm text-red-600">
                            {errors.gazetteer_node_id ?? errors.raw_text}
                        </p>
                    )}
                </div>
            </div>

            {!processing && (
                <div className="shrink-0 border-t border-stone-200 bg-white px-3 pt-3 pb-[max(0.75rem,env(safe-area-inset-bottom))]">
                    <form onSubmit={submit} className="mx-auto max-w-2xl">
                        <div className="mb-2">
                            <PlacePicker places={places} value={place} onChange={pickPlace} disabled={processing} />
                        </div>

                        <div className="flex items-end gap-2">
                            <input
                                ref={fileInput}
                                type="file"
                                accept="image/*"
                                className="hidden"
                                onChange={(e) => pickPhoto(e.target.files?.[0])}
                            />
                            <button
                                type="button"
                                onClick={() => fileInput.current?.click()}
                                aria-label="Attach a photo"
                                className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-lg text-stone-500 transition hover:bg-stone-100"
                            >
                                📎
                            </button>

                            <textarea
                                ref={textarea}
                                value={data.raw_text}
                                onChange={(e) => {
                                    setData('raw_text', e.target.value);
                                    grow(e.target);
                                }}
                                onKeyDown={onKeyDown}
                                rows={1}
                                dir={isUrdu(data.raw_text) ? 'rtl' : 'ltr'}
                                placeholder="Describe the problem…"
                                className="max-h-40 min-h-11 flex-1 resize-none rounded-2xl bg-stone-100 px-4 py-3 text-base text-stone-900 placeholder:text-stone-400 focus:bg-white focus:ring-1 focus:ring-accent-600 focus:outline-none"
                            />

                            <button
                                type="submit"
                                disabled={!canSend}
                                aria-label="Send report"
                                className="flex h-11 w-11 shrink-0 items-center justify-center rounded-full bg-accent-600 text-white transition hover:bg-accent-700 disabled:opacity-30"
                            >
                                <svg viewBox="0 0 24 24" className="h-5 w-5 fill-current" aria-hidden="true">
                                    <path d="M2.01 21 23 12 2.01 3 2 10l15 2-15 2z" />
                                </svg>
                            </button>
                        </div>

                        <div className="mt-2 flex flex-wrap items-center justify-between gap-x-4 gap-y-2">
                            <VoiceNoteInput onTranscript={appendTranscript} disabled={processing} compact />

                            <button
                                type="button"
                                onClick={() => setShowContact((v) => !v)}
                                className="text-xs text-stone-500 underline-offset-2 hover:underline"
                            >
                                {showContact ? 'Hide contact details' : 'Add your name or phone (optional)'}
                            </button>
                        </div>

                        {showContact && (
                            <div className="mt-2 grid grid-cols-1 gap-2 sm:grid-cols-2">
                                <input
                                    type="text"
                                    value={data.citizen_name}
                                    onChange={(e) => setData('citizen_name', e.target.value)}
                                    placeholder="Your name"
                                    className="min-h-11 rounded-xl bg-stone-100 px-4 text-base focus:bg-white focus:ring-1 focus:ring-accent-600 focus:outline-none"
                                />
                                <input
                                    type="tel"
                                    value={data.citizen_phone}
                                    onChange={(e) => setData('citizen_phone', e.target.value)}
                                    placeholder="Phone (shared only if you add it)"
                                    className="min-h-11 rounded-xl bg-stone-100 px-4 text-base focus:bg-white focus:ring-1 focus:ring-accent-600 focus:outline-none"
                                />
                            </div>
                        )}
                    </form>
                </div>
            )}
        </AppLayout>
    );
}
