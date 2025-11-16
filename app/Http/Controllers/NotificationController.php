<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends BaseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $list = Notification::whereHas(
            'user',
            fn($q) => $q->whereId($request->user()->id)
        )->get();

        return $this->successResponse('List of notifications', [
            'notifications' => NotificationResource::collection($list)
        ]);
    }
}
