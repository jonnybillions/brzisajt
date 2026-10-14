{{-- Kartica-opcija: pravi radio/checkbox unutar label. --}}
@props([
    'model',
    'value',
    'title',
    'text' => null,
    'type' => 'radio',
    'name' => null,
    'disabled' => false,
    'live' => false,
    'compact' => false,
])
<label {{ $attributes->class([
    'group relative flex cursor-pointer gap-3 rounded-2xl border-2 border-ink bg-paper transition-[background-color,box-shadow,transform] duration-200',
    'hover:-translate-y-0.5 hover:shadow-[0_4px_0_0_var(--c-ink)]',
    'has-checked:bg-violet has-checked:text-white has-checked:shadow-[0_4px_0_0_var(--c-ink)]',
    'has-focus-visible:outline-3 has-focus-visible:outline-offset-3 has-focus-visible:outline-violet',
    'has-disabled:cursor-not-allowed has-disabled:opacity-50 has-disabled:hover:translate-y-0 has-disabled:hover:shadow-none',
    $compact ? 'items-center px-4 py-3' : 'items-start p-4 sm:p-5',
]) }}>
    <input
        type="{{ $type }}"
        @if ($live) wire:model.live="{{ $model }}" @else wire:model="{{ $model }}" @endif
        value="{{ $value }}"
        name="{{ $name ?? $model }}"
        class="peer sr-only"
        @disabled($disabled)
    >
    <span aria-hidden="true" @class([
        'mt-0.5 grid size-5 shrink-0 place-items-center border-2 border-current transition-colors',
        'rounded-full' => $type === 'radio',
        'rounded-md' => $type === 'checkbox',
        'group-has-checked:bg-lime group-has-checked:border-lime group-has-checked:text-ink',
    ])>
        <svg class="size-3 opacity-0 group-has-checked:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
    </span>
    <span class="min-w-0 flex-1">
        @isset($icon)
            <span class="mb-3 block">{{ $icon }}</span>
        @endisset
        <span class="block font-bold leading-snug">{{ $title }}</span>
        @if ($text)
            <span class="mt-1 block text-[0.9375rem] leading-snug opacity-80">{{ $text }}</span>
        @endif
        {{ $slot }}
    </span>
</label>
