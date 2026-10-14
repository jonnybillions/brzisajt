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

class TeamOrderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.team.subject', ['number' => $this->order->number, 'name' => $this->order->name]),
            replyTo: $this->order->email ? [new Address($this->order->email, $this->order->name)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'mail.team-order', with: [
            'order' => $this->order,
            'choices' => OrderSummary::lines($this->order->config),
            'contacts' => OrderSummary::contacts($this->order),
            'price' => OrderSummary::price($this->order->extras ?? []),
            'adminUrl' => url('/admin/orders/'.$this->order->id),
        ]);
    }
}
