<?php

declare(strict_types=1);

namespace App\Models\Traits;

use App\Models\Admin;
use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Model
 */
trait HasYear
{
    protected static function bootHasYear(): void
    {

        static::addGlobalScope(new YearScope);
    }

    protected static function curentYear(): string
    {
        return Admin::primaryAdmin()->year();
    }
}
