<?php

namespace App\Models;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id
 * @property string $user
 * @property string $from
 * @property string $from_name
 * @property array $to
 * @property string|null $reply_to
 * @property array<int,string>|null $cc
 * @property string $message
 * @property string|null $text
 * @property string $subject
 * @property array $attachments
 * @property mixed $status
 * @property string|null $failed_reason
 * @property CarbonInterface $created_at
 * @property CarbonInterface|null $sent_at
 * @property string $year
 * @property string|null $id2
 * @property array<int,string>|null $social_securities
 * @property Family|null $family
 */
class EmailQueue extends Model
{
    protected $table = 'email_queue';

    protected $guarded = [];

    public $timestamps = false;

    private const string STATUS_PENDING = '0';
    private const string STATUS_SENT = '1';
    private const string STATUS_FAILED = '2';

    // protected static function booted(): void
    // {
    //     static::addGlobalScope('twoYears', function (Builder $builder): void {
    //         $admin = Admin::primaryAdmin();

    //         $builder->where("year", $admin->year)
    //             ->orWhere('year', $admin->year2);
    //     });
    // }

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
            'cc' => 'array',
            'attachments' => 'array',
            'social_securities' => 'array',
            'sent_at' => 'datetime',
            'created_at' => 'datetime'
        ];
    }
}
