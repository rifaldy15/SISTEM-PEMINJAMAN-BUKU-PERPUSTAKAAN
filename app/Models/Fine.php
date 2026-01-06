<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Fine extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'days_overdue',
        'amount_per_day',
        'total_amount',
        'is_paid',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount_per_day' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'is_paid' => 'boolean',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the transaction this fine belongs to
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Mark fine as paid
     */
    public function markAsPaid(?string $notes = null): void
    {
        $this->is_paid = true;
        $this->paid_at = now();
        if ($notes) {
            $this->notes = $notes;
        }
        $this->save();
    }

    /**
     * Get total unpaid fines
     */
    public static function totalUnpaid(): float
    {
        return self::where('is_paid', false)->sum('total_amount');
    }

    /**
     * Get total collected fines (paid)
     */
    public static function totalCollected(): float
    {
        return self::where('is_paid', true)->sum('total_amount');
    }

    /**
     * Scope for unpaid fines
     */
    public function scopeUnpaid($query)
    {
        return $query->where('is_paid', false);
    }

    /**
     * Scope for paid fines
     */
    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }
}
