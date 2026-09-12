import { useEffect, useRef, useState } from 'react';
import Icon from '@/Components/Icon';

/**
 * Browser speech-to-text via the Web Speech API.
 *
 * Chosen over a transcription provider because DeepSeek has no speech-to-text
 * and no second provider key is configured — config/ai.php points transcription
 * at OpenAI, but only DEEPSEEK_API_KEY is set. Web Speech needs no key, no
 * backend and no upload, and it supports ur-PK.
 *
 * The transcript is appended to the same raw_text the typed form uses, so every
 * pipeline stage downstream is unchanged. If recognition is unsupported or
 * fails, the textarea still works and the citizen simply types.
 */

const LANGUAGES = [
    { code: 'ur-PK', label: 'اردو' },
    { code: 'en-PK', label: 'English' },
];

function getRecognition() {
    if (typeof window === 'undefined') {
        return null;
    }

    return window.SpeechRecognition || window.webkitSpeechRecognition || null;
}

export default function VoiceNoteInput({ onTranscript, disabled = false, compact = false }) {
    const [supported, setSupported] = useState(true);
    const [listening, setListening] = useState(false);
    const [language, setLanguage] = useState('ur-PK');
    const [interim, setInterim] = useState('');
    const [error, setError] = useState(null);

    const recognition = useRef(null);
    // Read inside the recognition callbacks without re-subscribing them.
    const onTranscriptRef = useRef(onTranscript);
    onTranscriptRef.current = onTranscript;

    // Mobile browsers end a recognition session at every natural pause, whatever
    // continuous says. Without this the recorder stops mid-sentence and the
    // citizen has to keep tapping. onend restarts while this is true, so the
    // session survives pauses and only a real tap on Stop clears it.
    const wantListening = useRef(false);

    useEffect(() => {
        const Recognition = getRecognition();

        if (!Recognition) {
            setSupported(false);

            return undefined;
        }

        const instance = new Recognition();
        instance.continuous = true;
        instance.interimResults = true;
        instance.lang = language;

        instance.onresult = (event) => {
            let settled = '';
            let pending = '';

            for (let i = event.resultIndex; i < event.results.length; i += 1) {
                const chunk = event.results[i][0].transcript;

                if (event.results[i].isFinal) {
                    settled += chunk;
                } else {
                    pending += chunk;
                }
            }

            setInterim(pending);

            if (settled.trim()) {
                onTranscriptRef.current(settled.trim());
            }
        };

        instance.onerror = (event) => {
            setInterim('');

            // no-speech fires on ordinary silence, especially on mobile. It is not
            // a failure, so leave wantListening set and let onend restart.
            if (event.error === 'no-speech') {
                return;
            }

            wantListening.current = false;
            setListening(false);

            if (event.error === 'not-allowed' || event.error === 'service-not-allowed') {
                setError('Microphone access was blocked. Allow it in your browser settings, or type instead.');
            } else if (event.error === 'network') {
                setError('Speech recognition needs a connection and could not reach it. Type instead.');
            } else if (event.error !== 'aborted') {
                setError('Speech recognition stopped unexpectedly. You can type instead.');
            }
        };

        instance.onend = () => {
            setInterim('');

            if (!wantListening.current) {
                setListening(false);

                return;
            }

            // Restart after a pause-triggered end. start() throws if the previous
            // session has not fully torn down, so failing here just ends recording.
            try {
                instance.start();
            } catch {
                wantListening.current = false;
                setListening(false);
            }
        };

        recognition.current = instance;

        return () => {
            wantListening.current = false;
            instance.onresult = null;
            instance.onerror = null;
            instance.onend = null;
            instance.abort();
        };
    }, [language]);

    function toggle() {
        if (!recognition.current) {
            return;
        }

        if (listening) {
            wantListening.current = false;
            setListening(false);
            recognition.current.stop();

            return;
        }

        setError(null);

        try {
            recognition.current.start();
            wantListening.current = true;
            setListening(true);
        } catch {
            // start() throws if a previous session has not fully ended yet.
            wantListening.current = false;
            setListening(false);
        }
    }

    if (!supported) {
        return (
            <p className={compact ? 'text-xs text-stone-500' : 'text-sm text-stone-500'}>
                {compact
                    ? 'Voice not supported here — use your keyboard’s microphone key.'
                    : 'This browser cannot record voice notes. On a phone, most keyboards have their own microphone key that dictates into the box above. Otherwise just type.'}
            </p>
        );
    }

    // Compact sits inline under a chat composer, so it is one small control plus
    // a language toggle — no helper paragraph, no full-width button.
    if (compact) {
        return (
            <div className="flex min-w-0 flex-wrap items-center gap-2">
                <button
                    type="button"
                    onClick={toggle}
                    disabled={disabled}
                    aria-pressed={listening}
                    aria-label={listening ? 'Stop recording' : 'Record a voice note'}
                    className={`flex min-h-9 items-center gap-2 rounded-full px-3 text-xs font-medium transition select-none disabled:cursor-not-allowed disabled:opacity-40 ${
                        listening
                            ? 'bg-red-50 text-red-700'
                            : 'text-stone-600 hover:bg-stone-100'
                    }`}
                >
                    {listening ? (
                        <span
                            className="h-2 w-2 rounded-full bg-red-500"
                            style={{ animation: 'pulse 1.4s ease-in-out infinite' }}
                        />
                    ) : (
                        <Icon name="microphone" className="h-4 w-4" />
                    )}
                    {listening ? 'Stop' : 'Voice'}
                </button>

                {LANGUAGES.map((option) => (
                    <button
                        key={option.code}
                        type="button"
                        onClick={() => setLanguage(option.code)}
                        disabled={listening || disabled}
                        className={`min-h-9 rounded-full px-3 text-xs transition select-none disabled:cursor-not-allowed disabled:opacity-40 ${
                            language === option.code
                                ? 'bg-stone-900 text-white'
                                : 'text-stone-500 hover:bg-stone-100'
                        }`}
                    >
                        {option.label}
                    </button>
                ))}

                {listening && (
                    <span
                        dir={language === 'ur-PK' ? 'rtl' : 'ltr'}
                        className="min-w-0 flex-1 truncate text-xs text-stone-500"
                    >
                        {interim || 'Listening…'}
                    </span>
                )}

                {error && <span className="w-full text-xs text-red-600">{error}</span>}
            </div>
        );
    }

    return (
        <div>
            <div className="flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    onClick={toggle}
                    disabled={disabled}
                    aria-pressed={listening}
                    className={`flex min-h-11 flex-1 items-center justify-center gap-2 rounded-lg border px-4 py-3 text-sm font-medium transition select-none sm:flex-none sm:justify-start disabled:cursor-not-allowed disabled:opacity-40 ${
                        listening
                            ? 'border-accent-600 bg-accent-50 text-accent-700'
                            : 'border-stone-300 bg-white text-stone-700 hover:border-stone-400'
                    }`}
                >
                    <span
                        className={`h-2.5 w-2.5 rounded-full ${listening ? 'bg-red-500' : 'bg-stone-400'}`}
                        style={listening ? { animation: 'pulse 1.4s ease-in-out infinite' } : undefined}
                    />
                    {listening ? 'Stop recording' : 'Record a voice note'}
                </button>

                <div className="flex gap-1" role="group" aria-label="Recording language">
                    {LANGUAGES.map((option) => (
                        <button
                            key={option.code}
                            type="button"
                            onClick={() => setLanguage(option.code)}
                            disabled={listening || disabled}
                            className={`min-h-11 rounded-md px-4 text-sm transition select-none disabled:cursor-not-allowed disabled:opacity-40 ${
                                language === option.code
                                    ? 'bg-stone-900 text-white'
                                    : 'bg-stone-100 text-stone-600 hover:bg-stone-200'
                            }`}
                        >
                            {option.label}
                        </button>
                    ))}
                </div>
            </div>

            {listening && (
                <p className="mt-2 text-sm text-stone-500" dir={language === 'ur-PK' ? 'rtl' : 'ltr'}>
                    {interim || 'Listening — speak now.'}
                </p>
            )}

            {error && <p className="mt-2 text-sm text-red-600">{error}</p>}

            {!listening && !error && (
                <p className="mt-2 text-xs text-stone-500">
                    Speak in Urdu or English. Your words are added to the box above, where you can edit them.
                </p>
            )}
        </div>
    );
}
