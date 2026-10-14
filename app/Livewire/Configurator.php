<?php

namespace App\Livewire;

use App\Configurator\Options;
use App\Configurator\OrderRules;
use App\Livewire\Forms\OrderForm;
use App\Models\PageTemplate;
use App\Models\Project;
use App\Services\OrderService;
use App\Support\Captcha\CaptchaVerifier;
use App\Support\ImageVariants;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class Configurator extends Component
{
    use WithFileUploads;

    public const STEPS = 4;

    public OrderForm $form;

    public int $step = 1;

    #[Locked]
    public int $maxStep = 1;

    /** @var list<TemporaryUploadedFile> */
    public array $uploads = [];

    /** @var list<TemporaryUploadedFile> */
    public array $newUploads = [];

    /** Zamka za botove: polje je skriveno od ljudi. */
    public string $website = '';

    public string $captchaToken = '';

    #[Locked]
    public int $startedAt = 0;

    /** Stil je stigao iz „Želim ovaj stil“ — ne prepisujemo ga iz localStorage. */
    #[Locked]
    public ?string $styleFromQuery = null;

    #[Locked]
    public ?array $submitted = null;

    /** @param  string|null  $style  iz „Želim ovaj stil“ (?style=slug) */
    public function mount(?string $style = null): void
    {
        $this->startedAt = now()->getTimestamp();

        if (is_string($style) && Project::published()->where('slug', $style)->exists()) {
            $this->form->style = $this->styleFromQuery = $style;
        }
    }

    /** Vraćanje nacrta iz localStorage. */
    public function restore(array $data, int $step = 1): void
    {
        if ($this->submitted) {
            return;
        }

        $this->form->restore($data);

        if ($this->styleFromQuery) {
            $this->form->style = $this->styleFromQuery;
        }

        // Vraćamo na sačuvani korak samo ako su prethodni koraci ispravno popunjeni.
        $target = 1;
        while ($target < min($step, self::STEPS) && $this->stepIsValid($target)) {
            $target++;
        }
        $this->step = $this->maxStep = $target;
    }

    public function next(): void
    {
        $this->validateStep($this->step);

        $this->step = min(self::STEPS, $this->step + 1);
        $this->maxStep = max($this->maxStep, $this->step);
        $this->dispatch('cfg-step', step: $this->step);
    }

    public function back(): void
    {
        $this->step = max(1, $this->step - 1);
        $this->dispatch('cfg-step', step: $this->step);
    }

    public function goTo(int $step): void
    {
        if ($step >= 1 && $step <= $this->maxStep) {
            $this->step = $step;
            $this->dispatch('cfg-step', step: $this->step);
        }
    }

    public function addColor(): void
    {
        if (count($this->form->colors) < Options::MAX_COLORS) {
            $this->form->colors[] = ['#6B3BFF', '#C8F43A', '#3EC5FF'][count($this->form->colors)];
        }
    }

    public function removeColor(int $index): void
    {
        unset($this->form->colors[$index]);
        $this->form->colors = array_values($this->form->colors);

        if (! $this->form->colors) {
            $this->form->colorsAuto = true;
        }
    }

    public function updatedFormColorsAuto(bool $auto): void
    {
        if (! $auto && ! $this->form->colors) {
            $this->addColor();
        }
    }

    public function updatedNewUploads(): void
    {
        $limits = config('brzisajt.limits');

        $this->validate([
            'newUploads.*' => ['file', 'max:'.$limits['upload_max_kb'], 'mimes:'.implode(',', $limits['upload_mimes'])],
        ], [], ['newUploads.*' => __('validation.attributes.uploads.*')]);

        $this->uploads = [...$this->uploads, ...$this->newUploads];
        $this->newUploads = [];

        $this->validateOnly('uploads', OrderRules::forStep(3, $this->formArray()));
    }

    public function removeUpload(int $index): void
    {
        unset($this->uploads[$index]);
        $this->uploads = array_values($this->uploads);
        $this->resetErrorBag('uploads');
    }

    public function submit(OrderService $orders, CaptchaVerifier $captcha): void
    {
        if ($this->submitted) {
            return;
        }

        $ip = request()->ip();

        if ($this->website !== '') {
            Log::info('Bystrosite: honeypot triggered', ['ip' => $ip]);
            $this->addError('submit', __('cfg.errors.generic'));

            return;
        }

        if (now()->getTimestamp() - $this->startedAt < config('brzisajt.antispam.min_fill_seconds')) {
            $this->addError('submit', __('cfg.errors.too_fast'));

            return;
        }

        $key = 'order-submit:'.$ip;
        if (RateLimiter::tooManyAttempts($key, config('brzisajt.antispam.rate_limit_per_hour'))) {
            $this->addError('submit', __('cfg.errors.rate_limited'));

            return;
        }

        if ($captcha->enabled() && ! $captcha->verify($this->captchaToken, $ip)) {
            $this->addError('submit', __('cfg.errors.captcha'));

            return;
        }

        // Ponovo proveravamo sve korake: kod greške vraćamo na odgovarajući korak.
        foreach (range(1, self::STEPS) as $step) {
            try {
                $this->validateStep($step);
            } catch (ValidationException $e) {
                $this->step = $step;
                throw $e;
            }
        }

        $order = $orders->create($this->form, $this->uploads, $this->form->cloudLink, $ip, request()->userAgent());
        RateLimiter::hit($key, 3600);

        $this->submitted = ['number' => $order->number, 'name' => $order->name];
        $this->reset('uploads', 'newUploads');
        $this->dispatch('cfg-submitted', number: $order->number);
    }

    /** Šabloni stranica po tipu — za neobavezan izbor stila stranice. */
    #[Computed(persist: true, seconds: 600)]
    public function templates(): array
    {
        return PageTemplate::query()
            ->with('project')
            ->whereHas('project', fn ($q) => $q->where('is_published', true))
            ->orderBy('sort')
            ->get()
            ->groupBy(fn (PageTemplate $t) => $t->page_type->value)
            ->map(fn ($group) => $group->map(fn (PageTemplate $t) => [
                'id' => $t->id,
                'project' => $t->project->name,
                'thumb' => $this->thumb($t->image, $t->image_width),
            ])->values()->all())
            ->all();
    }

    #[Computed(persist: true, seconds: 600)]
    public function projects(): array
    {
        return Project::published()->get()->map(fn (Project $p) => [
            'slug' => $p->slug,
            'name' => $p->name,
            'thumb' => $this->thumb($p->cover, $p->cover_width),
        ])->all();
    }

    /** Zamena dok se komponenta učitava (nalazi se ispod prvog ekrana). */
    public function placeholder()
    {
        return view('livewire.configurator.placeholder');
    }

    public function render()
    {
        return view('livewire.configurator', [
            'captchaEnabled' => app(CaptchaVerifier::class)->enabled(),
        ]);
    }

    private function validateStep(int $step): void
    {
        $this->validate(OrderRules::forStep($step, $this->formArray()));
    }

    private function stepIsValid(int $step): bool
    {
        return ! Validator::make(
            ['form' => $this->formArray(), 'uploads' => []],
            OrderRules::forStep($step, $this->formArray()),
        )->fails();
    }

    private function formArray(): array
    {
        return $this->form->all();
    }

    private function thumb(?string $path, ?int $width): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->url(
            $width ? ImageVariants::variantPath($path, ImageVariants::widthsFor($width)[0], 'webp') : $path
        );
    }
}
