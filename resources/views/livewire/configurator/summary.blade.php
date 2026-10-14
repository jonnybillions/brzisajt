@php($base = __('cfg.sum.base', ['price' => money(settings()->int('base_price'))]))
{{-- Rezime: lepljiva kartica na desktopu, donji panel na mobilnom --}}
<div
    class="cfg-summary fixed inset-x-0 bottom-0 z-40 transition-transform duration-300 lg:static lg:z-auto lg:translate-y-0"
    :class="inView || sheetOpen ? 'translate-y-0' : 'translate-y-full'"
>
    <div class="on-dark rounded-t-[1.75rem] border-t-2 border-white/10 shadow-[0_-12px_40px_-12px_rgb(18_16_22/0.5)] lg:rounded-card lg:border-0 lg:shadow-none">
        {{-- Skupljena traka (mobilni) --}}
        <button type="button" class="flex w-full items-center gap-3 px-5 py-4 text-left lg:hidden" @click="sheetOpen = !sheetOpen" :aria-expanded="sheetOpen.toString()" aria-controls="cfg-summary-body">
            <span class="min-w-0 flex-1">
                <span class="block text-[0.75rem] font-bold uppercase tracking-wider text-muted-dark">{{ __('cfg.sum.title') }}</span>
                <span class="block truncate font-display text-[1.05rem] font-bold">{{ money(settings()->int('base_price')) }}<span x-show="extras.length" class="text-lime"> + <span x-text="extras.length"></span></span></span>
            </span>
            <span class="chip bg-lime text-[0.8125rem] text-ink" x-text="sheetOpen ? @js(__('cfg.sum.close')) : @js(__('cfg.sum.open'))">{{ __('cfg.sum.open') }}</span>
        </button>

        <div id="cfg-summary-body" class="max-h-[60vh] overflow-y-auto px-5 pb-6 lg:block lg:max-h-none lg:overflow-visible lg:p-7" :class="sheetOpen ? 'block' : 'hidden'" x-cloak>
            <h3 class="hidden text-[1.5rem] font-bold lg:block">{{ __('cfg.sum.title') }}</h3>

            <dl class="mt-2 divide-y divide-white/10 text-[0.9375rem] lg:mt-5">
                <template x-for="row in summary" :key="row.label">
                    <div class="grid grid-cols-[7.5rem_1fr] gap-3 py-2.5">
                        <dt class="text-muted-dark" x-text="row.label"></dt>
                        <dd class="font-semibold break-words" x-text="row.value"></dd>
                    </div>
                </template>
            </dl>

            <div class="mt-5 rounded-2xl bg-violet p-4 text-white" aria-live="polite">
                <p class="font-display text-[1.125rem] font-bold">{{ $base }}</p>
                <template x-for="extra in extras" :key="extra">
                    <p class="mt-1 font-bold"><span class="text-lime">+</span> <span x-text="extra"></span></p>
                </template>
            </div>
            <p class="mt-3 text-[0.8125rem] leading-snug text-muted-dark">{{ typo(__('cfg.sum.note')) }}</p>
        </div>
    </div>
</div>
