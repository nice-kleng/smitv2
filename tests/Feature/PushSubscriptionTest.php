<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Support\HelpdeskFixtures;

uses(RefreshDatabase::class);

it('stores a push subscription for a teknisi', function () {
    $user = HelpdeskFixtures::user('teknisi');

    $this->actingAs($user)->postJson(route('push.subscription.store'), [
        'endpoint' => 'https://push.example/one',
        'keys' => ['p256dh' => 'public-key', 'auth' => 'auth-token'],
        'contentEncoding' => 'aes128gcm',
    ])->assertNoContent();

    expect($user->pushSubscriptions()->count())->toBe(1);
});

it('rejects a user without the teknisi role', function () {
    $user = HelpdeskFixtures::user('unit');

    $this->actingAs($user)->postJson(route('push.subscription.store'), [
        'endpoint' => 'https://push.example/two',
        'keys' => ['p256dh' => 'public-key', 'auth' => 'auth-token'],
    ])->assertForbidden();
});

it('rejects a user without helpdesk permission', function () {
    $user = HelpdeskFixtures::user('teknisi', false);

    $this->actingAs($user)->postJson(route('push.subscription.store'), [
        'endpoint' => 'https://push.example/three',
        'keys' => ['p256dh' => 'public-key', 'auth' => 'auth-token'],
    ])->assertForbidden();
});

it('moves an existing endpoint to the current user', function () {
    $first = HelpdeskFixtures::user('teknisi');
    $second = HelpdeskFixtures::user('teknisi');
    $first->updatePushSubscription('https://push.example/shared', 'old-key', 'old-auth', 'aes128gcm');

    $this->actingAs($second)->postJson(route('push.subscription.store'), [
        'endpoint' => 'https://push.example/shared',
        'keys' => ['p256dh' => 'new-key', 'auth' => 'new-auth'],
        'contentEncoding' => 'aes128gcm',
    ])->assertNoContent();

    expect($first->pushSubscriptions()->count())->toBe(0)
        ->and($second->pushSubscriptions()->count())->toBe(1);
});

it('keeps only the newest subscription per user', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $user->updatePushSubscription('https://push.example/phone', 'key', 'auth', 'aes128gcm');

    $this->actingAs($user)->postJson(route('push.subscription.store'), [
        'endpoint' => 'https://push.example/tablet',
        'keys' => ['p256dh' => 'key', 'auth' => 'auth'],
        'contentEncoding' => 'aes128gcm',
    ])->assertNoContent();

    expect($user->pushSubscriptions()->count())->toBe(1)
        ->and($user->pushSubscriptions()->first()->endpoint)->toBe('https://push.example/tablet');
});

it('deletes all subscriptions on logout', function () {
    $user = HelpdeskFixtures::user('teknisi');
    $user->updatePushSubscription('https://push.example/logout', 'key', 'auth', 'aes128gcm');

    $this->actingAs($user)->post(route('logout'))->assertRedirect('/login');

    $this->assertDatabaseMissing('push_subscriptions', [
        'subscribable_type' => $user->getMorphClass(),
        'subscribable_id' => $user->id,
    ]);
});
