<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Models\User;
use App\Notifications\TicketCreatedNotification;
use Illuminate\Support\Facades\Notification;

class SendTicketCreatedPush
{
    public function handle(TicketCreated $event): void
    {
        defer(function () use ($event) {
            $recipients = User::role('teknisi')
                ->whereHas('pushSubscriptions')
                ->get();

            foreach ($recipients as $user) {
                try {
                    Notification::send($user, new TicketCreatedNotification($event->ticket));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        });
    }
}
