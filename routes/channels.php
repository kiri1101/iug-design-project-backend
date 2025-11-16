<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('presence.chat', fn($leave) => [
    'leave' => $leave
]);
