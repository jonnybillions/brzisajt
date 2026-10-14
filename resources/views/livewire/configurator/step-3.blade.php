@php
    use App\Support\Typo;
    $limits = config('brzisajt.limits');
    $totalBytes = collect($uploads)->sum(fn ($f) => $f->getSize());
    $fmt = fn (int $bytes) => $bytes >= 1048576 ? number_format($bytes / 1048576, 1, ',', '') . "\u{00A0}MB" : max(1, round($bytes / 1024)) . "\u{00A0}KB";
@endphp

{{-- Delatnost --}}
<x-cfg.question :title="__('cfg.industry.q')" as="div">
    <label for="cfg-industry" class="sr-only">{{ __('cfg.industry.q') }}</label>
    <select id="cfg-industry" wire:model="form.industry" class="cfg-input cfg-select max-w-md" @error('form.industry') aria-invalid="true" aria-describedby="err-form-industry" @enderror>
        <option value="">{{ __('cfg.industry.placeholder') }}</option>
        @foreach (__('cfg.industry.options') as $value => $label)
            <option value="{{ $value }}">{{ $label }}</option>
        @endforeach
    </select>
    <x-cfg.error field="form.industry" />
</x-cfg.question>

{{-- Jezici --}}
<x-cfg.question :title="__('cfg.langs.q')" error="form.langs">
    <div class="grid gap-3 sm:grid-cols-2">
        @foreach (__('cfg.langs.options') as $value => $label)
            <x-cfg.option model="form.langs" :value="$value" :title="$label" compact />
        @endforeach
    </div>
    <div x-show="f.langs === 'two'" x-cloak x-transition class="mt-4 max-w-md">
        <label for="cfg-second-lang" class="cfg-label">{{ __('cfg.langs.second') }}</label>
        <input id="cfg-second-lang" type="text" wire:model="form.secondLang" maxlength="60" placeholder="{{ __('cfg.langs.second_placeholder') }}" class="cfg-input"
               @error('form.secondLang') aria-invalid="true" aria-describedby="err-form-secondLang" @enderror>
        <x-cfg.error field="form.secondLang" />
    </div>
</x-cfg.question>

{{-- Funkcije --}}
<x-cfg.question :title="__('cfg.features.q')">
    <div class="grid gap-3 sm:grid-cols-2">
        <label class="flex items-center gap-3 rounded-2xl border-2 border-ink bg-violet px-4 py-3 text-white">
            <input type="checkbox" checked disabled class="peer sr-only" aria-describedby="feat-form-included">
            <span aria-hidden="true" class="grid size-5 shrink-0 place-items-center rounded-md border-2 border-lime bg-lime text-ink">
                <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 5 5L20 7"/></svg>
            </span>
            <span class="font-bold">{{ __('cfg.features.options.form') }}</span>
            <span id="feat-form-included" class="ms-auto rounded-full bg-lime px-2 py-0.5 text-[0.75rem] font-bold text-ink">{{ __('cfg.included') }}</span>
        </label>
        @foreach (\App\Configurator\Options::FEATURES as $value)
            <x-cfg.option type="checkbox" model="form.features" :value="$value" :title="__('cfg.features.options.'.$value)" name="form.features[]" compact />
        @endforeach
    </div>
</x-cfg.question>

{{-- Tekstovi, fotografije, logo --}}
<div class="grid gap-10 sm:grid-cols-2 sm:gap-6">
    <x-cfg.question :title="__('cfg.texts.q')" error="form.texts">
        <div class="grid gap-3">
            @foreach (__('cfg.texts.options') as $value => $label)
                <x-cfg.option model="form.texts" :value="$value" :title="$label" compact />
            @endforeach
        </div>
    </x-cfg.question>
    <x-cfg.question :title="__('cfg.images.q')" error="form.images">
        <div class="grid gap-3">
            @foreach (__('cfg.images.options') as $value => $label)
                <x-cfg.option model="form.images" :value="$value" :title="$label" compact />
            @endforeach
        </div>
    </x-cfg.question>
</div>

<x-cfg.question :title="__('cfg.logo.q')" error="form.logo">
    <div class="grid gap-3 sm:grid-cols-2">
        @foreach (\App\Configurator\Options::LOGOS as $value)
            <x-cfg.option model="form.logo" :value="$value" :title="typo(__('cfg.logo.options.'.$value, ['price' => Typo::money(settings()->int('extra_logo_price'))]))" compact />
        @endforeach
    </div>
</x-cfg.question>

<x-cfg.question :title="typo(__('cfg.seo.q', ['price' => Typo::money(settings()->int('extra_seo_price'))]))">
    <x-cfg.option type="checkbox" model="form.seo" value="1" :title="__('cfg.seo.label')" compact class="w-max max-w-full" />
</x-cfg.question>

{{-- Fajlovi --}}
<x-cfg.question :title="__('cfg.files.q')" :hint="typo(__('cfg.files.hint'))" as="div"
    x-data="{ progress: null, dragging: false }"
    x-on:livewire-upload-start="progress = 0"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
    x-on:livewire-upload-finish="progress = null"
    x-on:livewire-upload-error="progress = null">
    <label for="cfg-files"
           @dragover.prevent="dragging = true" @dragleave.prevent="dragging = false" @drop="dragging = false"
           :class="dragging && 'bg-violet/10 border-violet'"
           class="relative flex cursor-pointer flex-col items-center justify-center gap-1 rounded-2xl border-2 border-dashed border-ink/50 px-4 py-8 text-center transition-colors hover:border-ink has-focus-visible:outline-3 has-focus-visible:outline-offset-3 has-focus-visible:outline-violet">
        <svg class="size-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 16V4m0 0-4 4m4-4 4 4M4 16v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
        <span class="font-bold">{{ __('cfg.files.choose') }}</span>
        <span class="text-[0.875rem] text-muted">{{ __('cfg.files.drop') }} · {{ strtoupper(implode(', ', array_diff($limits['upload_mimes'], ['jpeg']))) }}</span>
        <input id="cfg-files" type="file" multiple wire:model="newUploads" class="absolute inset-0 cursor-pointer opacity-0"
               accept=".{{ implode(',.', $limits['upload_mimes']) }}" aria-describedby="cfg-files-total">
    </label>

    <div x-show="progress !== null" x-cloak class="mt-3 h-2 overflow-hidden rounded-full bg-line" role="progressbar" aria-label="{{ __('cfg.files.uploading') }}" :aria-valuenow="progress" aria-valuemin="0" aria-valuemax="100">
        <div class="h-full bg-violet transition-[width]" :style="`width: ${progress}%`"></div>
    </div>

    @if ($uploads)
        <ul class="mt-4 space-y-2">
            @foreach ($uploads as $i => $file)
                <li wire:key="upload-{{ $i }}-{{ $file->getFilename() }}" class="flex items-center gap-3 rounded-xl bg-cream px-3 py-2">
                    <svg class="size-5 shrink-0 text-violet" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5"/></svg>
                    <span class="min-w-0 flex-1 truncate text-[0.9375rem] font-semibold">{{ $file->getClientOriginalName() }}</span>
                    <span class="shrink-0 text-[0.8125rem] text-muted">{{ $fmt($file->getSize()) }}</span>
                    <button type="button" wire:click="removeUpload({{ $i }})" class="grid size-8 shrink-0 place-items-center rounded-full hover:bg-ink/10" aria-label="{{ __('cfg.files.remove') }}: {{ $file->getClientOriginalName() }}">
                        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg>
                    </button>
                </li>
            @endforeach
        </ul>
    @endif
    <p id="cfg-files-total" class="mt-2 text-[0.8125rem] text-muted">{{ __('cfg.files.total', ['size' => $fmt($totalBytes ?: 0)]) }}</p>
    <x-cfg.error field="uploads" />
    @foreach ($errors->getMessages() as $key => $messages)
        @if (str_starts_with($key, 'uploads.') || str_starts_with($key, 'newUploads'))
            <p class="mt-2 text-[0.9375rem] font-semibold text-[#B42318]" role="alert">{{ $messages[0] }}</p>
        @endif
    @endforeach

    <div class="mt-5 max-w-xl">
        <label for="cfg-cloud" class="cfg-label">{{ __('cfg.files.link') }}</label>
        <input id="cfg-cloud" type="url" inputmode="url" wire:model="form.cloudLink" placeholder="{{ __('cfg.files.link_placeholder') }}" class="cfg-input"
               @error('form.cloudLink') aria-invalid="true" aria-describedby="err-form-cloudLink" @enderror>
        <x-cfg.error field="form.cloudLink" />
    </div>
</x-cfg.question>
