<?php

namespace App\Casts;

use App\Dtos\StoreItemOption;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<StoreItemOption[], array>
 */
class StoreItemOptionsCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return StoreItemOption[]
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): array
    {
        if (empty($value)) {
            return [];
        }

        $decoded = is_string($value) ? json_decode($value, true) : $value;

        if (!is_array($decoded)) {
            return [];
        }

        return array_map(
            fn(array $item) => StoreItemOption::fromArray($item),
            $decoded
        );
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  Model  $model
     * @param  string  $key
     * @param  mixed  $value
     * @param  array  $attributes
     * @return string|null
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): ?string
    {
        if (empty($value)) {
            return null;
        }

        if (is_string($value)) {
            return $value;
        }

        $arrayValue = array_map(function ($item) {
            if ($item instanceof StoreItemOption) {
                return $item->toArray();
            }
            return $item;
        }, $value);

        return json_encode($arrayValue);
    }
}
