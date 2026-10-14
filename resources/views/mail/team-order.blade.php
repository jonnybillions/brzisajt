@component('mail.layout', ['title' => __('mail.team.subject', ['number' => $order->number, 'name' => $order->name])])
    <p style="margin:0;font-size:13px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:#5C5766;">{{ $order->number }} · {{ $order->created_at->timezone(config('app.timezone'))->format('d.m.Y H:i') }}</p>
    <h1 style="margin:8px 0 0;font-size:26px;line-height:1.2;">{{ $order->name }}@if ($order->company) <span style="color:#5C5766;font-weight:600;">— {{ $order->company }}</span>@endif</h1>
    <p style="margin:12px 0 0;font-size:15px;">{{ __('mail.team.intro') }}</p>

    <p style="margin:20px 0 0;display:inline-block;background:#C8F43A;border-radius:999px;padding:8px 16px;font-weight:800;font-size:15px;">{{ $price }}</p>

    @include('mail.partials.rows', ['title' => __('mail.contacts'), 'rows' => $contacts])
    @include('mail.partials.rows', ['title' => __('mail.choices'), 'rows' => $choices])

    @if ($order->files->isNotEmpty())
        <p style="margin:20px 0 0;font-size:15px;">{{ __('mail.team.files', ['count' => $order->files->count()]) }}</p>
    @endif

    <p style="margin:28px 0 0;">
        <a href="{{ $adminUrl }}" style="display:inline-block;background:#6B3BFF;color:#FFFFFF;text-decoration:none;font-weight:800;border-radius:999px;padding:14px 24px;">{{ __('admin.order.open') }}</a>
    </p>
@endcomponent
