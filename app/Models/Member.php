<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'member_number',
        'name',
        'email',
        'phone',
        'address',
        'class',
        'photo',
        'joined_at',
        'expired_at',
        'status',
        'max_borrow',
    ];

    protected $casts = [
        'joined_at' => 'date',
        'expired_at' => 'date',
        'max_borrow' => 'integer',
    ];

    /**
     * Get the user account for this member
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transactions for this member
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get all visits for this member
     */
    public function visits(): HasMany
    {
        return $this->hasMany(Visitor::class);
    }

    /**
     * Get active (unreturned) transactions
     */
    public function activeTransactions(): HasMany
    {
        return $this->hasMany(Transaction::class)->whereNull('returned_at');
    }

    /**
     * Check if membership is expired
     */
    public function getIsExpiredAttribute(): bool
    {
        return $this->expired_at->isPast();
    }

    /**
     * Check if member can borrow more books
     */
    public function getCanBorrowAttribute(): bool
    {
        if ($this->status !== 'active' || $this->is_expired) {
            return false;
        }
        return $this->activeTransactions()->count() < $this->max_borrow;
    }

    /**
     * Get remaining borrow quota
     */
    public function getRemainingQuotaAttribute(): int
    {
        return max(0, $this->max_borrow - $this->activeTransactions()->count());
    }

    /**
     * Get total unpaid fines
     */
    public function getUnpaidFinesAttribute(): float
    {
        return $this->transactions()
            ->join('fines', 'transactions.id', '=', 'fines.transaction_id')
            ->where('fines.is_paid', false)
            ->sum('fines.total_amount');
    }

    /**
     * Scope for active members
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('expired_at', '>=', now());
    }

    /**
     * Generate unique member number
     */
    public static function generateMemberNumber(): string
    {
        $year = date('Y');
        $lastMember = self::where('member_number', 'like', $year . '%')
            ->orderBy('member_number', 'desc')
            ->first();
        
        if ($lastMember) {
            $lastNumber = intval(substr($lastMember->member_number, 4));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return $year . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }
}
