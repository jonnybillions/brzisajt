<?php

use App\Jobs\SendTelegramNotification;
use App\Mail\ClientOrderMail;
use App\Mail\TeamOrderMail;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    Mail::fake();
    RateLimiter::clear('order-submit:127.0.0.1');
    config(['brzisajt.telegram.bot_token' => null, 'brzisajt.telegram.chat_id' => null]);
});

it('queues the team email with a readable summary', function () {
    filledConfigurator()->call('submit');

    Mail::assertQueued(TeamOrderMail::class, function (TeamOrderMail $mail) {
        $html = $mail->render();

        return $mail->hasTo('filip@otlr.digital')
            && $mail->hasReplyTo('ana@example.rs')
            && $mail->envelope()->subject === 'Novi upit BS-0001 — Ana'
            && str_contains($html, 'Višestranični')
            && str_contains($html, 'Početna, O nama, Usluge, Kontakt')
            && str_contains($html, '/admin/orders/'.Order::sole()->id);
    });
});

it('sends the team email to every configured recipient', function () {
    settings()->put(['notify_emails' => 'one@otlr.digital, two@otlr.digital, broken']);

    filledConfigurator()->call('submit');

    Mail::assertQueued(TeamOrderMail::class, fn ($mail) => $mail->hasTo('one@otlr.digital') && $mail->hasTo('two@otlr.digital') && ! $mail->hasTo('broken'));
});

it('sends the client confirmation when an email is given', function () {
    filledConfigurator()->call('submit');

    Mail::assertQueued(ClientOrderMail::class, function (ClientOrderMail $mail) {
        return $mail->hasTo('ana@example.rs')
            && $mail->envelope()->subject === 'Primili smo vaš upit BS-0001 — Brzisajt'
            && str_contains($mail->render(), 'Zdravo, Ana!');
    });
});

it('does not send a client confirmation without an email', function () {
    filledConfigurator(['email' => ''])->call('submit');

    Mail::assertQueued(TeamOrderMail::class);
    Mail::assertNotQueued(ClientOrderMail::class);
});

it('does not notify Telegram unless configured', function () {
    Queue::fake();

    filledConfigurator()->call('submit');

    Queue::assertNotPushed(SendTelegramNotification::class);
});

it('notifies Telegram when the token and chat are set', function () {
    Queue::fake();
    config(['brzisajt.telegram.bot_token' => '123:abc', 'brzisajt.telegram.chat_id' => '-100500']);

    filledConfigurator()->call('submit');

    Queue::assertPushed(SendTelegramNotification::class, fn ($job) => $job->order->number === 'BS-0001');
});

it('posts the order to the Telegram Bot API', function () {
    config(['brzisajt.telegram.bot_token' => '123:abc', 'brzisajt.telegram.chat_id' => '-100500']);
    Http::fake(['api.telegram.org/*' => Http::response(['ok' => true])]);

    $order = Order::unguarded(fn () => Order::create([
        'name' => 'Ana <b>', 'phone' => '+381 63', 'config' => [], 'extras' => ['logo'], 'consent_at' => now(),
    ]));

    (new SendTelegramNotification($order->refresh()))->handle();

    Http::assertSent(fn ($request) => $request->url() === 'https://api.telegram.org/bot123:abc/sendMessage'
        && $request['chat_id'] === '-100500'
        && str_contains($request['text'], 'BS-0001')
        && str_contains($request['text'], 'Ana &lt;b&gt;'));
});
