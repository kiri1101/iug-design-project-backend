<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Events\NotifyMessage;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function sendMessage(Request $request)
    {
        $user = User::first();
        $notify = Notification::first();

        broadcast(
            new NotifyMessage(
                $user,
                $notify->message,
            )
        )->toOthers();

        return response()->json([
            'message' => 'Message broadcasted'
        ]);
    }
}
