import { useEffect, useId, useMemo, useRef, useState } from 'react';

/**
 * Search-as-you-type over the TMC towns and union councils. Everything is
 * filtered in the browser — the whole list is ~185 rows and arrives as a
 * page prop, so there is no endpoint to fail on stage.
 */

// "Gulshan-e-Iqbal", "gulshan e iqbal" and "Gulshan-E-Iqbal." all fold to
// "gulshan e iqbal" so a citizen's spelling of the hyphens doesn't matter.
function fold(text) {
    return (text ?? '')
        .toLowerCase()
        .replace(/[.'’\-_/]/g, ' ')
        .replace(/\s+/g, ' ')
        .trim();
}

function matchStrength(haystacks, query) {
    let best = 0;

    for (const hay of haystacks.filter(Boolean).map(fold)) {
        if (hay === query) {
            best = Math.max(best, 4);
        } else if (hay.startsWith(query)) {
            best = Math.max(best, 3);
        } else if (hay.includes(` ${query}`)) {
            best = Math.max(best, 2);
        } else if (hay.includes(query)) {
            best = Math.max(best, 1);
        }
    }

    return best;
}

// A hit on the place's own name outranks a hit through its town, so typing
// "gulshan" lists Gulshan-e-Ghazi before Gulshan-e-Iqbal's own UCs.
function score(place, query) {
    return matchStrength([place.name, ...(place.aliases ?? [])], query) * 10 + matchStrength([place.town], query);
}

export function placeLabel(place) {
    if (!place) {
        return '';
    }

    return [place.name, place.kind === 'uc' ? place.town : null, place.district ? `District ${place.district}` : null]
        .filter(Boolean)
        .join(', ');
}

export default function PlacePicker({ places, value, onChange, disabled = false }) {
    const [query, setQuery] = useState('');
    const [open, setOpen] = useState(false);
    const [active, setActive] = useState(0);
    const input = useRef(null);
    const listId = useId();

    const results = useMemo(() => {
        const q = fold(query);

        if (q.length < 2) {
            return [];
        }

        return places
            .map((place) => ({ place, score: score(place, q) }))
            .filter((r) => r.score > 0)
            .sort((a, b) => {
                if (b.score !== a.score) {
                    return b.score - a.score;
                }

                if (a.place.kind !== b.place.kind) {
                    return a.place.kind === 'town' ? -1 : 1;
                }

                return a.place.name.localeCompare(b.place.name);
            })
            .slice(0, 8)
            .map((r) => r.place);
    }, [places, query]);

    useEffect(() => {
        setActive(0);
    }, [query]);

    function pick(place) {
        onChange(place);
        setQuery('');
        setOpen(false);
    }

    function clear() {
        onChange(null);
        setTimeout(() => input.current?.focus(), 0);
    }

    function onKeyDown(e) {
        if (!open || results.length === 0) {
            if (e.key === 'Escape') {
                setOpen(false);
            }

            return;
        }

        if (e.key === 'ArrowDown') {
            e.preventDefault();
            setActive((i) => Math.min(i + 1, results.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive((i) => Math.max(i - 1, 0));
        } else if (e.key === 'Enter') {
            e.preventDefault();
            pick(results[active]);
        } else if (e.key === 'Escape') {
            setOpen(false);
        }
    }

    if (value) {
        return (
            <div className="flex min-h-11 items-center gap-2 rounded-2xl bg-accent-50 px-3 py-2 text-sm text-accent-800">
                <span aria-hidden="true">📍</span>
                <span className="min-w-0 flex-1 truncate">
                    <span className="font-medium">{value.name}</span>
                    {value.kind === 'uc' && <span className="text-accent-700">, {value.town}</span>}
                    {value.district && <span className="text-accent-700"> · District {value.district}</span>}
                </span>
                <button
                    type="button"
                    onClick={clear}
                    disabled={disabled}
                    aria-label="Change location"
                    className="flex h-8 w-8 shrink-0 items-center justify-center rounded-full text-accent-700 transition hover:bg-accent-100"
                >
                    ✕
                </button>
            </div>
        );
    }

    const showList = open && fold(query).length >= 2;

    return (
        <div className="relative">
            <input
                ref={input}
                type="text"
                role="combobox"
                aria-expanded={showList}
                aria-controls={listId}
                aria-activedescendant={showList && results[active] ? `${listId}-${results[active].id}` : undefined}
                aria-autocomplete="list"
                autoComplete="off"
                spellCheck={false}
                value={query}
                disabled={disabled}
                onChange={(e) => {
                    setQuery(e.target.value);
                    setOpen(true);
                }}
                onFocus={() => setOpen(true)}
                onBlur={() => setTimeout(() => setOpen(false), 120)}
                onKeyDown={onKeyDown}
                placeholder="Where is this? Town or UC name…"
                className="min-h-11 w-full rounded-2xl bg-stone-100 px-4 py-2.5 text-base text-stone-900 placeholder:text-stone-400 focus:bg-white focus:ring-1 focus:ring-accent-600 focus:outline-none"
            />

            {showList && (
                <ul
                    id={listId}
                    role="listbox"
                    className="absolute right-0 bottom-full left-0 z-20 mb-2 max-h-72 overflow-y-auto rounded-2xl border border-stone-200 bg-white py-1 shadow-lg"
                >
                    {results.length === 0 && (
                        <li className="px-4 py-3 text-sm text-stone-500">
                            No town or UC matches — try the nearest town.
                        </li>
                    )}

                    {results.map((place, i) => (
                        <li
                            key={place.id}
                            id={`${listId}-${place.id}`}
                            role="option"
                            aria-selected={i === active}
                            onMouseDown={(e) => e.preventDefault()}
                            onMouseEnter={() => setActive(i)}
                            onClick={() => pick(place)}
                            className={`cursor-pointer px-4 py-2.5 ${i === active ? 'bg-accent-50' : ''}`}
                        >
                            <p className="text-sm font-medium text-stone-900">{place.name}</p>
                            <p className="text-xs text-stone-500">
                                {place.kind === 'uc'
                                    ? `${place.uc_code} · ${place.town} · District ${place.district}`
                                    : `Town · District ${place.district}`}
                            </p>
                        </li>
                    ))}
                </ul>
            )}
        </div>
    );
}
