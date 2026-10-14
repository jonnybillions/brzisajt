@php
    use App\Support\Typo;
    $labels = [
        'pages' => __('cfg.pages.options'),
        'type' => ['onepage' => __('cfg.type.onepage'), 'multipage' => __('cfg.type.multipage')],
        'hero' => __('cfg.hero.options'),
        'menuType' => __('cfg.menu.type.options'),
        'menuPosition' => __('cfg.menu.position.options'),
        'motion' => collect(__('cfg.motion.options'))->map(fn ($o) => $o['title'])->all(),
        'industry' => __('cfg.industry.options'),
        'langs' => __('cfg.langs.options'),
        'features' => __('cfg.features.options'),
        'sum' => __('cfg.sum'),
        'styleTrust' => __('cfg.style.trust'),
        'colorsAuto' => __('cfg.colors.auto'),
        'extraLogo' => __('cfg.sum.extra_logo', ['price' => Typo::money(settings()->int('extra_logo_price'))]),
        'extraSeo' => Typo::apply(__('cfg.sum.extra_seo', ['price' => Typo::money(settings()->int('extra_seo_price'))])),
        'projects' => $this->projects,
    ];
@endphp
<div
    data-configurator
    x-data="configurator(@js(['labels' => $labels, 'submitted' => (bool) $submitted]))"
    x-intersect:enter.margin.-200px="inView = true"
    x-intersect:leave.margin.-200px="inView = false"
    @cfg-style.window="pickStyle($event.detail.slug)"
    class="mt-10 lg:mt-14"
>
    @if ($submitted)
        {{-- Uspeh --}}
        <div class="card relative overflow-hidden bg-lime text-ink lg:p-14" role="status" tabindex="-1" x-init="$el.focus()">
            <span aria-hidden="true" class="sticker absolute top-6 right-6 hidden rounded-full bg-violet px-5 py-3 text-[0.9rem] text-white sm:inline-grid" style="--r: 8deg">{{ $submitted['number'] }}</span>
            <h3 class="text-[clamp(2rem,5vw,3.5rem)] font-extrabold">{{ __('cfg.ok.title') }}</h3>
            <p class="mt-5 max-w-2xl text-[1.125rem] leading-relaxed lg:text-[1.25rem]">
                {{ typo(__('cfg.ok.text', ['name' => $submitted['name'], 'number' => $submitted['number']])) }}
            </p>
            <a href="{{ route('home') }}" class="btn btn-primary mt-8">{{ __('cfg.ok.back') }}</a>
        </div>
    @else
        <div class="grid items-start gap-6 lg:grid-cols-[minmax(0,1fr)_23rem] lg:gap-8 xl:grid-cols-[minmax(0,1fr)_26rem]">

            {{-- Forma --}}
            <div class="card order-2 border-2 border-ink bg-paper !p-5 sm:!p-8 lg:order-1 lg:!p-10">
                {{-- Napredak --}}
                <nav aria-label="{{ __('cfg.progress_label') }}">
                    <ol class="grid grid-cols-4 gap-2">
                        @foreach (__('cfg.steps') as $n => $label)
                            @php($state = $n === $step ? 'current' : ($n <= $maxStep ? 'done' : 'todo'))
                            <li>
                                <button type="button"
                                        wire:click="goTo({{ $n }})"
                                        @disabled($state === 'todo')
                                        @if ($state === 'current') aria-current="step" @endif
                                        class="group block w-full text-left disabled:cursor-default">
                                    <span @class([
                                        'block h-2 rounded-full transition-colors duration-300',
                                        'bg-violet' => $state === 'current',
                                        'bg-ink group-hover:bg-violet' => $state === 'done',
                                        'bg-line' => $state === 'todo',
                                    ])></span>
                                    <span @class([
                                        'mt-2 block text-[0.8125rem] font-bold leading-tight sm:text-[0.9375rem]',
                                        'text-ink' => $state !== 'todo',
                                        'text-muted' => $state === 'todo',
                                    ])>
                                        <span class="hidden sm:inline">{{ $n }}.</span> {{ $label }}
                                    </span>
                                </button>
                            </li>
                        @endforeach
                    </ol>
                </nav>

                <form wire:submit="submit" novalidate class="mt-8 lg:mt-10">
                    <div wire:key="step-{{ $step }}" class="cfg-step">
                        <p class="eyebrow text-muted">{{ __('cfg.step_of', ['n' => $step]) }}</p>
                        <h3 data-step-heading tabindex="-1" class="mt-1 text-[1.75rem] font-extrabold outline-none lg:text-[2.25rem]">{{ __('cfg.steps')[$step] }}</h3>

                        <div class="mt-8 space-y-10">
                            @include('livewire.configurator.step-'.$step)
                        </div>
                    </div>

                    {{-- Zamka za botove --}}
                    <div class="absolute -left-[9999px] h-px w-px overflow-hidden" aria-hidden="true">
                        <label for="cfg-website">{{ __('cfg.honeypot') }}</label>
                        <input id="cfg-website" type="text" wire:model="website" name="website" tabindex="-1" autocomplete="off">
                    </div>

                    <x-cfg.error field="submit" />

                    <div class="mt-10 flex flex-col-reverse gap-3 border-t-2 border-line pt-6 sm:flex-row sm:items-center sm:justify-between">
                        @if ($step > 1)
                            <button type="button" wire:click="back" class="btn btn-ghost">← {{ __('cfg.back') }}</button>
                        @else
                            <span></span>
                        @endif

                        @if ($step < 4)
                            <button type="button" wire:click="next" wire:loading.attr="disabled" wire:target="next" class="btn btn-primary">
                                {{ __('cfg.next') }} →
                            </button>
                        @else
                            <div class="flex flex-col items-stretch gap-2 sm:items-end">
                                <button type="submit" wire:loading.attr="disabled" wire:target="submit" class="btn btn-primary text-[1.0625rem]">
                                    <span wire:loading.remove wire:target="submit">{{ __('cfg.submit') }}</span>
                                    <span wire:loading wire:target="submit">{{ __('cfg.submitting') }}</span>
                                </button>
                            </div>
                        @endif
                    </div>
                    @if ($step === 4)
                        <p class="mt-4 text-[0.9375rem] text-muted sm:text-right">{{ typo(__('cfg.submit_note')) }}</p>
                    @endif
                </form>
            </div>

            {{-- Pregled + rezime --}}
            <aside class="order-1 lg:sticky lg:top-24 lg:order-2" aria-label="{{ __('cfg.sum.title') }}">
                @if ($step <= 2)
                    <div class="mb-6">
                        <button type="button" class="btn btn-ghost w-full !min-h-11 lg:hidden" @click="previewOpen = !previewOpen" :aria-expanded="previewOpen.toString()" aria-controls="cfg-preview">
                            <span x-text="previewOpen ? @js(__('cfg.preview.toggle_hide')) : @js(__('cfg.preview.toggle_show'))">{{ __('cfg.preview.toggle_show') }}</span>
                        </button>
                        <div id="cfg-preview" class="mt-4 hidden lg:mt-0 lg:block" :class="previewOpen && '!block'">
                            @include('livewire.configurator.preview')
                        </div>
                    </div>
                @endif

                @include('livewire.configurator.summary')
            </aside>
        </div>
    @endif
</div>
