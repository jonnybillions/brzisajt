@php
    $max = \App\Configurator\Options::maxPages();
    $templates = $this->templates;
@endphp

{{-- Tip sajta --}}
<x-cfg.question :title="__('cfg.type.q')" error="form.type">
    <div class="grid gap-3 sm:grid-cols-2">
        @foreach (['onepage', 'multipage'] as $type)
            <x-cfg.option model="form.type" :value="$type" :title="__('cfg.type.'.$type)" :text="typo(__('cfg.type.'.$type.'_text'))" live>
                <x-slot:icon>
                    {{-- Mini ilustracija --}}
                    <span class="flex h-16 w-24 gap-1 rounded-lg border-2 border-current p-1.5" aria-hidden="true">
                        @if ($type === 'onepage')
                            <span class="flex w-full flex-col gap-1">
                                <span class="h-2 rounded-sm bg-current opacity-80"></span>
                                <span class="flex-1 rounded-sm bg-current opacity-40"></span>
                                <span class="h-2 rounded-sm bg-current opacity-60"></span>
                                <span class="h-2 rounded-sm bg-current opacity-30"></span>
                            </span>
                        @else
                            @foreach ([80, 50, 30] as $o)
                                <span class="flex flex-1 flex-col gap-1 rounded-sm border border-current p-0.5" style="opacity: {{ $o / 100 + .2 }}">
                                    <span class="h-1.5 rounded-sm bg-current"></span>
                                    <span class="flex-1 rounded-sm bg-current opacity-50"></span>
                                </span>
                            @endforeach
                        @endif
                    </span>
                </x-slot:icon>
            </x-cfg.option>
        @endforeach
    </div>
</x-cfg.question>

{{-- Smenjivanje sekcija (samo jednostranični) --}}
@if ($form->type === 'onepage')
    <x-cfg.question :title="__('cfg.scroll.q')" error="form.scroll" class="cfg-reveal">
        <div class="grid gap-3 sm:grid-cols-3">
            @foreach (__('cfg.scroll.options') as $value => $label)
                <x-cfg.option model="form.scroll" :value="$value" :title="$label" compact />
            @endforeach
        </div>
    </x-cfg.question>
@endif

{{-- Stranice --}}
<x-cfg.question :title="__('cfg.pages.q')" :hint="typo(__('cfg.pages.hint'))" error="form.pages">
    <p class="mb-4 inline-flex items-center gap-2 rounded-full bg-ink px-3 py-1 text-[0.875rem] font-bold text-cream" aria-live="polite">
        <span x-text="@js(__('cfg.pages.counter')).replace(':n', pagesCount)">{{ __('cfg.pages.counter', ['n' => count($form->pages)]) }}</span>
    </p>

    <ul class="grid gap-3 sm:grid-cols-2">
        @foreach (__('cfg.pages.options') as $key => $label)
            @php($locked = $key === 'home')
            <li wire:key="page-{{ $key }}" class="min-w-0">
                <label @class([
                    'flex items-center gap-3 rounded-2xl border-2 border-ink bg-paper px-4 py-3 transition-colors',
                    'has-checked:bg-violet has-checked:text-white has-focus-visible:outline-3 has-focus-visible:outline-offset-3 has-focus-visible:outline-violet',
                    'has-disabled:opacity-100' => $locked,
                    'cursor-pointer has-disabled:cursor-not-allowed has-disabled:opacity-45' => ! $locked,
                ])>
                    <input type="checkbox" value="{{ $key }}" wire:model.live="form.pages" class="peer sr-only"
                           @if ($locked) checked disabled aria-describedby="page-home-locked" @else
                           :disabled="pagesCount >= {{ $max }} && !(f.pages || []).includes(@js($key))" @endif>
                    <span aria-hidden="true" class="grid size-5 shrink-0 place-items-center rounded-md border-2 border-current peer-checked:border-lime peer-checked:bg-lime peer-checked:text-ink">
                        <svg class="size-3 opacity-0 [:checked~*_&]:opacity-100" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
                    </span>
                    <span class="min-w-0 font-bold">{{ $label }}</span>
                    @if ($locked)
                        <span id="page-home-locked" class="ms-auto flex items-center gap-1 text-[0.8125rem] font-semibold opacity-80">
                            <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><rect x="5" y="11" width="14" height="9" rx="2"/><path d="M8 11V8a4 4 0 0 1 8 0v3"/></svg>
                            {{ __('cfg.pages.locked') }}
                        </span>
                    @endif
                </label>

                @if (in_array($key, $form->pages, true))
                    @if ($key === 'custom')
                        <div class="mt-2">
                            <label for="cfg-custom-page" class="sr-only">{{ __('cfg.pages.custom_label') }}</label>
                            <input id="cfg-custom-page" type="text" wire:model="form.customPage" maxlength="60"
                                   placeholder="{{ __('cfg.pages.custom_placeholder') }}"
                                   class="cfg-input" @error('form.customPage') aria-invalid="true" aria-describedby="err-form-customPage" @enderror>
                            <x-cfg.error field="form.customPage" />
                        </div>
                    @elseif (! empty($templates[$key]))
                        {{-- Neobavezan izbor stila stranice — podrazumevano skupljen --}}
                        <div x-data="{ open: false }" class="mt-1.5 ps-1">
                            <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-controls="tpl-{{ $key }}"
                                    class="inline-flex items-center gap-1 rounded-md text-[0.875rem] font-semibold text-violet underline decoration-dotted underline-offset-4 hover:decoration-solid">
                                <span>{{ __('cfg.pages.style_link') }}</span>
                                <svg class="size-3.5 transition-transform" :class="open && 'rotate-180'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" aria-hidden="true"><path d="m6 9 6 6 6-6"/></svg>
                            </button>
                            <fieldset id="tpl-{{ $key }}" x-show="open" x-collapse x-cloak class="mt-2">
                                <legend class="sr-only">{{ __('cfg.pages.style_link') }}: {{ $label }}</legend>
                                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4">
                                    <label class="flex aspect-[16/10] cursor-pointer items-center justify-center rounded-xl border-2 border-dashed border-ink/40 p-1 text-center text-[0.75rem] leading-tight font-bold has-checked:border-solid has-checked:border-violet has-checked:bg-violet/10 has-focus-visible:outline-3 has-focus-visible:outline-violet">
                                        <input type="radio" class="sr-only" name="tpl-{{ $key }}" value="" wire:model="form.pageStyles.{{ $key }}">
                                        {{ __('cfg.pages.style_default') }}
                                    </label>
                                    @foreach ($templates[$key] as $tpl)
                                        <label class="group relative block cursor-pointer overflow-hidden rounded-xl border-2 border-ink/20 has-checked:border-violet has-checked:ring-2 has-checked:ring-violet has-focus-visible:outline-3 has-focus-visible:outline-violet" title="{{ $tpl['project'] }}">
                                            <input type="radio" class="sr-only" name="tpl-{{ $key }}" value="{{ $tpl['id'] }}" wire:model="form.pageStyles.{{ $key }}">
                                            <img src="{{ $tpl['thumb'] }}" alt="" loading="lazy" width="480" height="300" class="aspect-[16/10] w-full object-cover object-top">
                                            <span class="block truncate bg-paper px-2 py-1 text-[0.75rem] font-semibold">{{ $tpl['project'] }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </fieldset>
                        </div>
                    @endif
                @endif
            </li>
        @endforeach
    </ul>

    <p x-show="pagesCount >= {{ $max }}" x-cloak x-transition class="mt-4 flex items-start gap-3 rounded-2xl bg-sky px-4 py-3 text-[0.9375rem] font-semibold text-ink" role="status">
        <span aria-hidden="true" class="text-[1.25rem] leading-none">👋</span>
        <span>{{ typo(__('cfg.pages.limit')) }}</span>
    </p>
</x-cfg.question>
