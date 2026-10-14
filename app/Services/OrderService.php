<?php

namespace App\Services;

use App\Jobs\SendTelegramNotification;
use App\Livewire\Forms\OrderForm;
use App\Mail\ClientOrderMail;
use App\Mail\TeamOrderMail;
use App\Models\Order;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class OrderService
{
    /**
     * Čuva upit (podaci su već provereni), fajlove — na privatni disk,
     * zatim stavlja obaveštenja u red.
     *
     * @param  list<UploadedFile>  $uploads
     */
    public function create(OrderForm $form, array $uploads, ?string $cloudLink, ?string $ip, ?string $userAgent): Order
    {
        $order = DB::transaction(function () use ($form, $uploads, $cloudLink, $ip, $userAgent) {
            $order = Order::create([
                'name' => trim($form->name),
                'company' => filled($form->company) ? trim($form->company) : null,
                'phone' => filled($form->phone) ? trim($form->phone) : null,
                'email' => filled($form->email) ? Str::lower(trim($form->email)) : null,
                'channel' => $form->channel,
                'config' => $form->config(),
                'extras' => $form->extras(),
                'comment' => filled($form->comment) ? trim($form->comment) : null,
                'cloud_link' => filled($cloudLink) ? trim($cloudLink) : null,
                'consent_at' => now(),
                'ip' => $ip,
                'user_agent' => $userAgent ? Str::limit($userAgent, 500, '') : null,
            ]);

            foreach ($uploads as $file) {
                $order->files()->create([
                    'path' => $file->storeAs((string) $order->id, Str::random(24).'.'.Str::lower($file->getClientOriginalExtension()), 'orders'),
                    'original_name' => Str::limit($file->getClientOriginalName(), 200, ''),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);
            }

            return $order->refresh();
        });

        $this->notify($order);

        return $order;
    }

    public function notify(Order $order): void
    {
        $recipients = settings()->notifyEmails();
        if ($recipients) {
            Mail::to($recipients)->queue(new TeamOrderMail($order));
        }

        if ($order->email) {
            Mail::to($order->email, $order->name)->queue(new ClientOrderMail($order));
        }

        if (SendTelegramNotification::configured()) {
            SendTelegramNotification::dispatch($order);
        }
    }
}
