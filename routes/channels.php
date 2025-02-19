<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('ticket', function ($user) {
    return $user->pu_kd === 'it' ?? false;
});
