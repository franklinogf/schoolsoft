<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        static::addGlobalScope('twoYears', function (Builder $builder): void {
            $admin = Admin::primaryAdmin();

            $builder->where("year", $admin->year)
                ->orWhere('year', $admin->year2);
        });
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

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class, 'id2', 'id');
    }

    protected function casts(): array
    {
        return [
            'to' => 'array',
            'attachments' => 'array',
            'social_securities' => 'array',
            'sent_at' => 'datetime',
            'created_at' => 'datetime'
        ];
    }
}
