<?php

use App\Events\TicketCreated;
use App\Listeners\SendTicketCreatedPush;
use App\Notifications\TicketCreatedNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Defer\DeferredCallbackCollection;
use Illuminate\Support\Facades\Notification;
use Tests\Support\HelpdeskFixtures;

uses(RefreshDatabase::class);

it('sends push only to teknisi with subscriptions', function () {
    Notification::fake();
    $teknisi = HelpdeskFixtures::user('teknisi');
    $teknisi->updatePushSubscription('https://push.example/teknisi', 'key', 'auth', 'aes128gcm');
    $direktur = HelpdeskFixtures::user('direktur');
    $direktur->updatePushSubscription('https://push.example/direktur', 'key', 'auth', 'aes128gcm');
    $ticket = HelpdeskFixtures::ticket();

    (new SendTicketCreatedPush)->handle(new TicketCreated($ticket));
    app(DeferredCallbackCollection::class)->invoke();

    Notification::assertSentTo($teknisi, TicketCreatedNotification::class);
    Notification::assertNotSentTo($direktur, TicketCreatedNotification::class);
});

it('builds the ticket payload and deep link', function () {
    $teknisi = HelpdeskFixtures::user('teknisi');
    $ticket = HelpdeskFixtures::ticket();

    $notification = new TicketCreatedNotification($ticket);
    $payload = $notification->toWebPush($teknisi, $notification)->toArray();

    expect($payload['title'])->toBe('Pengaduan Baru')
        ->and($payload['body'])->toContain($ticket->kd_ticket)
        ->and($payload['body'])->toContain('ICU')
        ->and($payload['body'])->toContain('Ruang 2')
        ->and($payload['data']['ticket_id'])->toBe($ticket->id)
        ->and($payload['data']['url'])->toBe("/pwa/helpdesk/pengaduan/{$ticket->id}");
});

it('keeps ticket creation successful when push fails', function () {
    config(['broadcasting.default' => 'null']);
    $teknisi = HelpdeskFixtures::user('teknisi');
    $teknisi->updatePushSubscription('https://push.example/failure', 'key', 'auth', 'aes128gcm');
    $existing = HelpdeskFixtures::ticket();

    Notification::shouldReceive('send')
        ->once()
        ->andThrow(new RuntimeException('push down'));

    $response = $this->post(route('helpdesk.store-ticket'), [
        'ruangan_id' => $existing->ruangan_id,
        'detail_aduan' => 'Printer macet',
    ]);

    // Kernel terminate sudah menjalankan deferred callback; jangan dipanggil dua kali.
    $response->assertRedirect(route('helpdesk.antrean'));
    $this->assertDatabaseHas('tickets', ['detail_aduan' => 'Printer macet']);
});