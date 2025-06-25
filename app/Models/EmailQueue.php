<?php

namespace App\Models;

use App\Models\Scopes\YearScope;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class EmailQueue extends Model
{
    protected $table = 'email_queue';

    protected $guarded = [];

    public $timestamps = false;

    private const string STATUS_PENDING = '0';
    private const string STATUS_SENT = '1';
    private const string STATUS_FAILED = '2';

    protected static function booted(): void
    {
        static::addGlobalScope(new YearScope);
    }

    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => Carbon::now(),
            'failed_reason' => null,
        ]);
    }

    public function markAsFailed(string $reason): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
            'sent_at' => null,
            'failed_reason' => $reason,
        ]);
    }


    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeSent(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeFailed(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    protected function casts(): array
    {
        return [
            'to' => 'array',
            'attachments' => 'array',
            'sent_at' => 'datetime',
        ];
    }
}
