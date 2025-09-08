<?php

declare(strict_types=1);

namespace App\Http\Resources\Api\V1\Restrictions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\CompanyUserRestriction */
final class CompanyUserRestrictionResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $model = $this->resource;
        /** @var \App\Models\CompanyUserRestriction $model */

        return [
            'id' => $model->getKey(),
            'user_id' => $model->user_id,
            'database_connection_id' => $model->database_connection_id,
            'read_only' => (bool) ($model->read_only ?? false),
        ];
    }
}
