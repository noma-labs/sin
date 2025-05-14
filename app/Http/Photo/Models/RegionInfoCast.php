<?php

declare(strict_types=1);

namespace App\Photo\Models;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

final class RegionInfoCast implements CastsAttributes
{
    public function get(Model $model, string $key, $value, array $attributes)
    {
        if (is_array($value)) {
            return RegionInfo::fromArray($value);
        }
        $arr = json_decode((string) $value, true);

        return RegionInfo::fromArray($arr);
    }

    public function set(Model $model, string $key, $value, array $attributes)
    {
        if ($value instanceof RegionInfo) {
            return json_encode($value->toArray());
        }

        return json_encode($value);
    }
}
