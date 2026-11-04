<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Inventory\Models\Ticket;
use Tests\Support\HelpdeskFixtures;

uses(RefreshDatabase::class);

it('renders the action form', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $ticket = HelpdeskFixtures::ticket();

    $this->actingAs($user)
        ->get(route('inventory.pwa.pengaduan.tindakan', $ticket->id))
        ->assertOk()
        ->assertSee('Simpan tindakan')
        ->assertSee('Service luar');
});

it('stores an action and completes the ticket', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $ticket = HelpdeskFixtures::ticket();

    $this->actingAs($user)->putJson(route('inventory.helpdesk.ticket.tindakan', $ticket->id), [
        'jenis_aduan_id' => $ticket->jenis_aduan_id,
        'tindak_lanjut' => 'AC dibersihkan',
        'keterangan_perbaikan' => '1',
    ])->assertOk()->assertJson(['success' => true]);

    $ticket->refresh();
    expect($ticket->status)->toBe('1')
        ->and($ticket->teknisi_id)->toBe($user->id);

    $this->assertDatabaseHas('log_books', [
        'service_id' => $ticket->id,
        'user_id' => $user->id,
    ]);
});

it('rejects service luar without an inventory', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $ticket = HelpdeskFixtures::ticket();

    $this->actingAs($user)->putJson(route('inventory.helpdesk.ticket.tindakan', $ticket->id), [
        'jenis_aduan_id' => $ticket->jenis_aduan_id,
        'tindak_lanjut' => 'Diservice',
        'keterangan_perbaikan' => '4',
    ])->assertStatus(422)
        ->assertJsonValidationErrors(['inventaris_id', 'tempat_service', 'kerusakan']);
});

it('creates a history service for external repair', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $ticket = HelpdeskFixtures::ticket();
    $inventory = HelpdeskFixtures::inventory($ticket->ruangan);

    $this->actingAs($user)->putJson(route('inventory.helpdesk.ticket.tindakan', $ticket->id), [
        'inventaris_id' => $inventory->id,
        'jenis_aduan_id' => $ticket->jenis_aduan_id,
        'tindak_lanjut' => 'Diservice',
        'keterangan_perbaikan' => '4',
        'tempat_service' => 'Toko AC',
        'kerusakan' => 'Kompresor',
        'biaya' => 250000,
    ])->assertOk();

    $this->assertDatabaseHas('history_services', [
        'inventaris_id' => $inventory->id,
        'tempat_service' => 'Toko AC',
        'biaya' => 250000,
    ]);
});

it('returns a safe label for an unknown repair code', function () {
    $ticket = Ticket::make(['keterangan_perbaikan' => '9']);

    expect($ticket->keterangan_perbaikan)->toBe('-');
});
