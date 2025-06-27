<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'pagos';
    protected $primaryKey = 'mt';
    public $timestamps = false;
    protected $guarded = [];
    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public static function nextReceiptNumber(): int
    {
        // Get the maximum receipt number from the payments table, ignoring the year scope
        $maxReceipt = self::withoutGlobalScope(YearScope::class)->max('rec');

        // If no receipts exist, start from 1, otherwise increment the max by 1
        return $maxReceipt ? $maxReceipt + 1 : 1;
    }
}
