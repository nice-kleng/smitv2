<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\HelpdeskFixtures;

uses(RefreshDatabase::class);

it('redirects a teknisi to the pwa queue after login', function () {
    $user = HelpdeskFixtures::user('teknisi');

    $this->post('/login', [
        'username' => $user->username,
        'password' => 'password',
    ])->assertRedirect(route('inventory.pwa.pengaduan.index'));
});

it('returns to the ticket after login from a notification link', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $ticket = HelpdeskFixtures::ticket();
    $url = route('inventory.pwa.pengaduan.show', $ticket->id);

    $this->get($url)->assertRedirect(route('login'))
        ->assertSessionHas('url.intended', $url);

    $this->withSession(['url.intended' => $url])
        ->post('/login', [
            'username' => $user->username,
            'password' => 'password',
        ])
        ->assertRedirect($url);
});

it('shows the mode hp link on the old helpdesk page', function () {
    $user = HelpdeskFixtures::user('teknisi');

    $this->actingAs($user)
        ->get(route('inventory.helpdesk.ticket.index'))
        ->assertOk()
        ->assertSee('Mode HP')
        ->assertDontSee('Notification.requestPermission', false)
        ->assertDontSee('new Notification(', false);
});