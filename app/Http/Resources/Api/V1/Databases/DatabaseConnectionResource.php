<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Databases;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatabaseConnectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var array<string, mixed> $data */
        $data = (array) parent::toArray($request);

        return $data;
    }
}
