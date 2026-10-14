<?php

namespace App\Jobs;

use App\Configurator\OrderSummary;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

/**
 * Kratko obaveštenje o upitu u Telegram čet tima.
 * Radi samo ako su podešeni TELEGRAM_BOT_TOKEN i TELEGRAM_CHAT_ID.
 */
class SendTelegramNotification implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public function __construct(public Order $order) {}

    public static function configured(): bool
    {
        return filled(config('brzisajt.telegram.bot_token')) && filled(config('brzisajt.telegram.chat_id'));
    }

    public function handle(): void
    {
        if (! self::configured()) {
            return;
        }

        $config = config('brzisajt.telegram');

        Http::timeout(10)
            ->post("https://api.telegram.org/bot{$config['bot_token']}/sendMessage", [
                'chat_id' => $config['chat_id'],
                'text' => $this->text(),
                'parse_mode' => 'HTML',
                'disable_web_page_preview' => true,
            ])
            ->throw();
    }

    public function text(): string
    {
        $o = $this->order;
        $esc = fn (?string $s) => htmlspecialchars((string) $s, ENT_QUOTES | ENT_HTML5);

        $lines = ["🆕 <b>{$esc(__('mail.team.subject', ['number' => $o->number, 'name' => $o->name]))}</b>", ''];
        foreach (OrderSummary::contacts($o) as $row) {
            $lines[] = "{$esc($row['label'])}: {$esc($row['value'])}";
        }
        $lines[] = '';
        $lines[] = $esc(OrderSummary::price($o->extras ?? []));
        $lines[] = '';
        $lines[] = $esc(url('/admin/orders/'.$o->id));

        return implode("\n", $lines);
    }
}
