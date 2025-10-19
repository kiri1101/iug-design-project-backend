<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->uuid,
            'fullName' => "$this->first_name $this->last_name",
            'mailingAddress' => $this->email,
            'phoneNumber' => $this->phone,
        ];
    }
}
