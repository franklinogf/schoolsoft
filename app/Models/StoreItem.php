<?php

namespace App\Models;

use App\Casts\StoreItemOptionsCast;
use App\Dtos\StoreItemOption;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $store_id
 * @property string $name
 * @property float $price
 * @property StoreItemOption[] $options
 * @property int $buy_multiple
 * @property string $picture_url
 * @property string $created_at
 * @property string $updated_at
 * @property Store|null $store
 */
class StoreItem extends Model
{
    public $timestamps = false;
    protected $guarded = [];


    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    protected function casts()
    {
        return [
            'options' => StoreItemOptionsCast::class,
        ];
    }

    /**
     * Find an option by name
     */
    public function findOption(string $name): ?StoreItemOption
    {
        foreach ($this->options as $option) {
            if ($option->name === $name) {
                return $option;
            }
        }
        return null;
    }

    /**
     * Get the price for a specific option, or the base price if not found
     */
    public function getPriceForOption(string $optionName): float
    {
        $option = $this->findOption($optionName);
        return $option?->price ?? $this->price;
    }
}
