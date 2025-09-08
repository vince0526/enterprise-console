<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
final class UserResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        /** @var array<string, mixed> $out */
        $out = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => null,
            'roles' => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),
            'created_at' => null,
            'updated_at' => null,
            'deleted_at' => null,
        ];

        // Safely format date fields if they are DateTimeInterface
        if ($this->email_verified_at instanceof \DateTimeInterface) {
            $out['email_verified_at'] = $this->email_verified_at->format(DATE_ATOM);
        }

        if ($this->created_at instanceof \DateTimeInterface) {
            $out['created_at'] = $this->created_at->format(DATE_ATOM);
        }

        if ($this->updated_at instanceof \DateTimeInterface) {
            $out['updated_at'] = $this->updated_at->format(DATE_ATOM);
        }

        if ($this->deleted_at instanceof \DateTimeInterface) {
            $out['deleted_at'] = $this->deleted_at->format(DATE_ATOM);
        }

        return $out;
    }
}
