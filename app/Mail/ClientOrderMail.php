<?php

namespace App\Mail;

use App\Configurator\OrderSummary;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ClientOrderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        $replyTo = settings('email');

        return new Envelope(
            subject: __('mail.client.subject', ['number' => $this->order->number]),
            replyTo: $replyTo ? [new Address($replyTo, __('site.brand.name'))] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.client-order', with: [
            'order' => $this->order,
            'choices' => OrderSummary::lines($this->order->config),
            'price' => OrderSummary::price($this->order->extras ?? []),
        ]);
    }
}
