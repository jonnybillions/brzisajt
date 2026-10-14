@component('mail.layout', ['title' => __('mail.client.subject', ['number' => $order->number])])
    <h1 style="margin:0;font-size:26px;line-height:1.2;">{{ __('mail.client.greeting', ['name' => $order->name]) }}</h1>
    <p style="margin:14px 0 0;font-size:16px;line-height:1.55;">{{ typo(__('mail.client.body')) }}</p>

    <p style="margin:22px 0 0;font-size:15px;">
        {{ __('mail.client.number') }}:
        <span style="display:inline-block;background:#C8F43A;border-radius:999px;padding:6px 14px;font-weight:800;">{{ $order->number }}</span>
    </p>

    @include('mail.partials.rows', ['title' => __('mail.choices'), 'rows' => $choices])

    <p style="margin:22px 0 0;font-size:15px;"><strong>{{ __('mail.price') }}:</strong> {{ $price }}</p>
    <p style="margin:6px 0 0;font-size:14px;color:#5C5766;">{{ typo(__('mail.client.price_note')) }}</p>

    <p style="margin:24px 0 0;font-size:15px;line-height:1.55;">
        {{ __('mail.client.questions') }}
        <a href="{{ tel_href(settings('phone')) }}" style="color:#6B3BFF;font-weight:700;white-space:nowrap;">{{ settings('phone') }}</a>
    </p>
@endcomponent
