<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'check_in',
        'check_out',
        'purpose',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    /**
     * Get the member (visitor)
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get visit duration in minutes
     */
    public function getDurationAttribute(): ?int
    {
        if (!$this->check_out) {
            return null;
        }
        return $this->check_in->diffInMinutes($this->check_out);
    }

    /**
     * Check in a member
     */
    public static function checkIn(Member $member, ?string $purpose = null): self
    {
        return self::create([
            'member_id' => $member->id,
            'check_in' => now(),
            'purpose' => $purpose,
        ]);
    }

    /**
     * Check out current visit
     */
    public function checkOut(): void
    {
        $this->check_out = now();
        $this->save();
    }

    /**
     * Get today's visitor count
     */
    public static function todayCount(): int
    {
        return self::whereDate('check_in', today())->count();
    }

    /**
     * Get total visitors for a date range
     */
    public static function countBetween(Carbon $start, Carbon $end): int
    {
        return self::whereBetween('check_in', [$start, $end])->count();
    }

    /**
     * Scope for today's visitors
     */
    public function scopeToday($query)
    {
        return $query->whereDate('check_in', today());
    }

    /**
     * Scope for visitors still in library (not checked out)
     */
    public function scopeStillIn($query)
    {
        return $query->whereNull('check_out');
    }
}
