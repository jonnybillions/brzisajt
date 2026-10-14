@php($projects = $this->projects)

{{-- Opšti stil --}}
<x-cfg.question :title="__('cfg.style.q')" error="form.style">
    <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
        <label class="flex aspect-[16/11] cursor-pointer flex-col justify-between rounded-2xl border-2 border-ink bg-lime p-4 text-ink transition-transform hover:-translate-y-0.5 has-checked:shadow-[0_4px_0_0_var(--c-ink)] has-checked:ring-4 has-checked:ring-violet has-focus-visible:outline-3 has-focus-visible:outline-offset-3 has-focus-visible:outline-violet">
            <input type="radio" class="sr-only" name="form.style" value="trust" wire:model="form.style">
            <span class="text-[1.75rem]" aria-hidden="true">🤝</span>
            <span>
                <span class="block font-bold leading-tight">{{ __('cfg.style.trust') }}</span>
                <span class="mt-0.5 block text-[0.8125rem] leading-snug">{{ __('cfg.style.trust_text') }}</span>
            </span>
        </label>
        @foreach ($projects as $p)
            <label wire:key="style-{{ $p['slug'] }}" class="group relative block cursor-pointer overflow-hidden rounded-2xl border-2 border-ink bg-paper transition-transform hover:-translate-y-0.5 has-checked:shadow-[0_4px_0_0_var(--c-ink)] has-checked:ring-4 has-checked:ring-violet has-focus-visible:outline-3 has-focus-visible:outline-offset-3 has-focus-visible:outline-violet">
                <input type="radio" class="sr-only" name="form.style" value="{{ $p['slug'] }}" wire:model="form.style">
                @if ($p['thumb'])
                    <img src="{{ $p['thumb'] }}" alt="" loading="lazy" width="480" height="300" class="aspect-[16/10] w-full border-b-2 border-ink object-cover object-top">
                @endif
                <span class="block px-3 py-2 text-[0.8125rem] font-bold leading-tight">
                    <span class="sr-only">{{ __('cfg.style.like') }} </span>{{ $p['name'] }}
                </span>
                <span aria-hidden="true" class="absolute top-2 right-2 grid size-7 scale-0 place-items-center rounded-full bg-violet text-white transition-transform group-has-checked:scale-100">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                </span>
            </label>
        @endforeach
    </div>
</x-cfg.question>

{{-- Početni ekran --}}
<x-cfg.question :title="__('cfg.hero.q')" error="form.hero">
    <div class="grid gap-3 sm:grid-cols-3">
        @foreach (__('cfg.hero.options') as $value => $label)
            <x-cfg.option model="form.hero" :value="$value" :title="$label" compact />
        @endforeach
    </div>
</x-cfg.question>

{{-- Meni --}}
<div class="grid gap-10 sm:grid-cols-2 sm:gap-6">
    <x-cfg.question :title="__('cfg.menu.type.q')" error="form.menuType">
        <div class="grid gap-3">
            @foreach (__('cfg.menu.type.options') as $value => $label)
                <x-cfg.option model="form.menuType" :value="$value" :title="$label" compact />
            @endforeach
        </div>
    </x-cfg.question>
    <x-cfg.question :title="__('cfg.menu.position.q')" error="form.menuPosition">
        <div class="grid gap-3">
            @foreach (__('cfg.menu.position.options') as $value => $label)
                <x-cfg.option model="form.menuPosition" :value="$value" :title="$label" compact />
            @endforeach
        </div>
    </x-cfg.question>
</div>

{{-- Animacije --}}
<x-cfg.question :title="__('cfg.motion.q')" error="form.motion">
    <div class="grid gap-3 sm:grid-cols-3">
        @foreach (__('cfg.motion.options') as $value => $o)
            <x-cfg.option model="form.motion" :value="$value" :title="$o['title']" :text="$o['text']" />
        @endforeach
    </div>
</x-cfg.question>

{{-- Boje --}}
<x-cfg.question :title="__('cfg.colors.q')" error="form.colors">
    <label class="inline-flex cursor-pointer items-center gap-3 rounded-full border-2 border-ink px-4 py-2 font-bold has-checked:bg-ink has-checked:text-cream has-focus-visible:outline-3 has-focus-visible:outline-offset-3 has-focus-visible:outline-violet">
        <input type="checkbox" wire:model.live="form.colorsAuto" class="sr-only">
        <span aria-hidden="true" class="grid size-5 place-items-center rounded-md border-2 border-current [:checked~&]:bg-lime [:checked~&]:border-lime [:checked~&]:text-ink">
            <svg class="size-3 opacity-0 [:checked~*_&]:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
        </span>
        {{ __('cfg.colors.auto') }}
    </label>

    @unless ($form->colorsAuto)
        <div class="mt-4 flex flex-wrap items-center gap-3">
            @foreach ($form->colors as $i => $color)
                <div wire:key="color-{{ $i }}" class="flex items-center gap-2 rounded-full border-2 border-ink bg-paper py-1 ps-1 pe-2">
                    <label class="sr-only" for="cfg-color-{{ $i }}">{{ __('cfg.colors.color_n', ['n' => $i + 1]) }}</label>
                    <input id="cfg-color-{{ $i }}" type="color" wire:model="form.colors.{{ $i }}" class="size-10 cursor-pointer rounded-full border-0 bg-transparent p-0 [&::-webkit-color-swatch]:rounded-full [&::-webkit-color-swatch]:border-2 [&::-webkit-color-swatch]:border-ink [&::-webkit-color-swatch-wrapper]:p-0 [&::-moz-color-swatch]:rounded-full">
                    <span class="font-mono text-[0.875rem] uppercase" x-text="(f.colors || [])[{{ $i }}]">{{ $color }}</span>
                    <button type="button" wire:click="removeColor({{ $i }})" class="grid size-7 place-items-center rounded-full hover:bg-ink/10" aria-label="{{ __('cfg.colors.remove') }} {{ $i + 1 }}">
                        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                </div>
            @endforeach
            @if (count($form->colors) < \App\Configurator\Options::MAX_COLORS)
                <button type="button" wire:click="addColor" class="chip border-2 border-dashed border-ink hover:bg-ink/5">+ {{ __('cfg.colors.add') }}</button>
            @endif
        </div>
        @foreach ($form->colors as $i => $color)
            <x-cfg.error :field="'form.colors.'.$i" />
        @endforeach
    @endunless
</x-cfg.question>
