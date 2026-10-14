@props(['title', 'hint' => null, 'as' => 'fieldset', 'error' => null])
<{{ $as }} {{ $attributes->class('min-w-0') }} @if ($as === 'fieldset' && $error) @error($error) aria-describedby="err-{{ str_replace(['.', '*'], '-', $error) }}" @enderror @endif>
    @if ($as === 'fieldset')
        <legend class="text-[1.125rem] font-bold leading-snug lg:text-[1.25rem]">{{ $title }}</legend>
    @else
        <h4 class="text-[1.125rem] font-bold leading-snug lg:text-[1.25rem]">{{ $title }}</h4>
    @endif
    @if ($hint)
        <p class="mt-1 text-[0.9375rem] text-muted">{{ $hint }}</p>
    @endif
    <div class="mt-4">{{ $slot }}</div>
    @if ($error)
        <x-cfg.error :field="$error" />
    @endif
</{{ $as }}>
