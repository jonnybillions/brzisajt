@props(['field'])
@error($field)
    <p id="err-{{ str_replace(['.', '*'], '-', $field) }}" class="mt-2 flex items-start gap-1.5 text-[0.9375rem] font-semibold text-[#B42318]" role="alert">
        <svg class="mt-0.5 size-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M12 8v5M12 16h.01" stroke-linecap="round"/></svg>
        <span>{{ $message }}</span>
    </p>
@enderror
