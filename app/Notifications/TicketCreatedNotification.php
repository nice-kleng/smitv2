<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Modules\Inventory\Models\Ticket;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TicketCreatedNotification extends Notification
{
    public function __construct(public Ticket $ticket)
    {
    }

    public function via(object $notifiable): array
    {
        return [WebPushChannel::class];
    }

    public function toWebPush(object $notifiable, Notification $notification): WebPushMessage
    {
        $ticket = $this->ticket->loadMissing('ruangan.unit');

        return (new WebPushMessage)
            ->title('Pengaduan Baru')
            ->body("{$ticket->kd_ticket} · {$ticket->ruangan->unit->nama_unit} / {$ticket->ruangan->nama_ruangan}")
            ->icon('/img/icon-192.png')
            ->badge('/img/icon-192.png')
            ->lang('id')
            ->tag("ticket-{$ticket->id}")
            ->requireInteraction(true)
            ->data([
                'ticket_id' => $ticket->id,
                'url' => "/pwa/helpdesk/pengaduan/{$ticket->id}",
            ]);
    }
}