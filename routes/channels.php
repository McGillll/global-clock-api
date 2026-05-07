<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel for each countdown sequence
Broadcast::channel('countdown.{sequenceId}', function ($user, $sequenceId) {
    // Allow access to anyone with a valid token
    // In production, verify user owns the sequence or has share token
    return true;
});

// Public channel for shared countdown (via token)
Broadcast::channel('shared-countdown.{token}', function ($user, $token) {
    // Anyone with the token can listen
    return true;
});
