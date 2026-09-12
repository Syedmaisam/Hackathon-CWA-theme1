import { useForm, Head } from '@inertiajs/react';
import { useRef, useState } from 'react';
import AppLayout from '@/Layouts/AppLayout';

const STEPS = ['Reading your report', 'Finding who owns this', 'Writing the complaint'];

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        raw_text: '',
        citizen_name: '',
        citizen_phone: '',
        photo: null,
    });

    const [photoPreview, setPhotoPreview] = useState(null);
    const [dragging, setDragging] = useState(false);
    const [showContact, setShowContact] = useState(false);
    const fileInput = useRef(null);

    function pickPhoto(file) {
        if (!file) return;
        setData('photo', file);
        setPhotoPreview(URL.createObjectURL(file));
    }

    function submit(e) {
        e.preventDefault();
        post('/reports', { forceFormData: true });
    }

    return (
        <AppLayout>
            <Head title="Report a problem" />

            <h1 className="text-2xl font-semibold text-stone-900">What's broken near you?</h1>
            <p className="mt-2 text-stone-600">
                Describe it in your own words — English, Urdu, or a mix. We'll work out who's
                responsible and write the complaint for you.
            </p>

            {processing ? (
                <div className="mt-10 space-y-4">
                    {STEPS.map((step, i) => (
                        <div key={step} className="flex items-center gap-3">
                            <span
                                className="h-2.5 w-2.5 shrink-0 rounded-full bg-accent-500"
                                style={{ animation: `pulse 1.4s ease-in-out ${i * 0.3}s infinite` }}
                            />
                            <span className="h-4 flex-1 max-w-xs animate-pulse rounded bg-stone-200" />
                            <span className="sr-only">{step}</span>
                        </div>
                    ))}
                    <p className="pt-2 text-sm text-stone-500">{STEPS[0]}…</p>
                </div>
            ) : (
                <form onSubmit={submit} className="mt-8 space-y-6">
                    <div>
                        <textarea
                            value={data.raw_text}
                            onChange={(e) => setData('raw_text', e.target.value)}
                            rows={6}
                            placeholder="e.g. Sewage overflowing on our street since Sunday, near the Nazimabad No. 2 chowk..."
                            className="w-full resize-none rounded-lg border border-stone-300 p-4 text-base text-stone-900 placeholder:text-stone-400 focus:border-accent-600 focus:outline-none focus:ring-1 focus:ring-accent-600"
                        />
                        {errors.raw_text && <p className="mt-1 text-sm text-red-600">{errors.raw_text}</p>}
                    </div>

                    <div
                        onDragOver={(e) => {
                            e.preventDefault();
                            setDragging(true);
                        }}
                        onDragLeave={() => setDragging(false)}
                        onDrop={(e) => {
                            e.preventDefault();
                            setDragging(false);
                            pickPhoto(e.dataTransfer.files?.[0]);
                        }}
                        onClick={() => fileInput.current?.click()}
                        className={`flex cursor-pointer items-center gap-4 rounded-lg border-2 border-dashed p-4 transition ${
                            dragging ? 'border-accent-600 bg-accent-50' : 'border-stone-300'
                        }`}
                    >
                        <input
                            ref={fileInput}
                            type="file"
                            accept="image/*"
                            className="hidden"
                            onChange={(e) => pickPhoto(e.target.files?.[0])}
                        />
                        {photoPreview ? (
                            <img src={photoPreview} alt="" className="h-16 w-16 rounded object-cover" />
                        ) : (
                            <div className="flex h-16 w-16 items-center justify-center rounded bg-stone-100 text-stone-400">
                                📷
                            </div>
                        )}
                        <div>
                            <p className="text-sm font-medium text-stone-700">
                                {photoPreview ? 'Photo attached — click to change' : 'Add a photo (optional)'}
                            </p>
                            <p className="text-xs text-stone-500">Drag and drop, or click to browse</p>
                        </div>
                    </div>

                    <div>
                        <button
                            type="button"
                            onClick={() => setShowContact((v) => !v)}
                            className="text-sm text-accent-700 underline-offset-2 hover:underline"
                        >
                            {showContact ? 'Hide contact details' : 'Add your name or phone (optional)'}
                        </button>
                        {showContact && (
                            <div className="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2">
                                <input
                                    type="text"
                                    value={data.citizen_name}
                                    onChange={(e) => setData('citizen_name', e.target.value)}
                                    placeholder="Your name"
                                    className="rounded-lg border border-stone-300 p-3 text-sm focus:border-accent-600 focus:outline-none focus:ring-1 focus:ring-accent-600"
                                />
                                <input
                                    type="tel"
                                    value={data.citizen_phone}
                                    onChange={(e) => setData('citizen_phone', e.target.value)}
                                    placeholder="Phone (shared only if you add it)"
                                    className="rounded-lg border border-stone-300 p-3 text-sm focus:border-accent-600 focus:outline-none focus:ring-1 focus:ring-accent-600"
                                />
                            </div>
                        )}
                    </div>

                    <button
                        type="submit"
                        disabled={!data.raw_text.trim()}
                        className="w-full rounded-lg bg-accent-600 px-6 py-3 text-base font-medium text-white transition hover:bg-accent-700 disabled:cursor-not-allowed disabled:opacity-40"
                    >
                        Submit report
                    </button>
                </form>
            )}
        </AppLayout>
    );
}
