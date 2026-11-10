<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\HelpdeskFixtures;

uses(RefreshDatabase::class);

it('renders pending tickets only', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $pending = HelpdeskFixtures::ticket(['detail_aduan' => 'AC rusak']);
    $done = HelpdeskFixtures::ticket(['status' => '1', 'detail_aduan' => 'Lampu mati']);

    $this->actingAs($user)
        ->get(route('inventory.pwa.pengaduan.index'))
        ->assertOk()
        ->assertSee('Antrean pengaduan')
        ->assertSee($pending->kd_ticket)
        ->assertDontSee($done->kd_ticket);
});

it('opens a completed ticket by id', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $done = HelpdeskFixtures::ticket(['status' => '1']);

    $this->actingAs($user)
        ->get(route('inventory.pwa.pengaduan.show', $done->id))
        ->assertOk()
        ->assertSee($done->kd_ticket)
        ->assertSee('Selesai');
});

it('renders a pending ticket without a technician', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $ticket = HelpdeskFixtures::ticket(['teknisi_id' => null]);

    $this->actingAs($user)
        ->get(route('inventory.pwa.pengaduan.show', $ticket->id))
        ->assertOk()
        ->assertSee('Menunggu');
});

it('forbids users without helpdesk permission', function () {
    $user = HelpdeskFixtures::user('unit', false);
    $ticket = HelpdeskFixtures::ticket();

    $this->actingAs($user)
        ->get(route('inventory.pwa.pengaduan.index'))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('inventory.pwa.pengaduan.show', $ticket->id))
        ->assertForbidden();

    $this->actingAs($user)
        ->get(route('inventory.helpdesk.ticket.index'))
        ->assertForbidden();
});

it('renders pwa shell metadata', function () {
    $user = HelpdeskFixtures::user('teknisi');

    $this->actingAs($user)
        ->get(route('inventory.pwa.pengaduan.index'))
        ->assertOk()
        ->assertSee('manifest.webmanifest', false)
        ->assertSee('vapid-public-key', false);
});
