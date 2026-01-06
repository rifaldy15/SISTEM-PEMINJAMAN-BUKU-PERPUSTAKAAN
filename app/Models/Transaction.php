<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'book_id',
        'borrowed_at',
        'due_date',
        'returned_at',
        'status',
        'notes',
    ];

    protected $casts = [
        'borrowed_at' => 'date',
        'due_date' => 'date',
        'returned_at' => 'date',
    ];

    /**
     * Get the member who borrowed
     */
    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /**
     * Get the borrowed book
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the fine for this transaction
     */
    public function fine(): HasOne
    {
        return $this->hasOne(Fine::class);
    }

    /**
     * Check if transaction is overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->returned_at) {
            return false;
        }
        return $this->due_date->isPast();
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return $this->due_date->diffInDays(now());
    }

    /**
     * Calculate fine amount based on config
     */
    public function calculateFineAmount(float $amountPerDay = 500): float
    {
        return $this->days_overdue * $amountPerDay;
    }

    /**
     * Process book return
     */
    public function processReturn(): void
    {
        $this->returned_at = now();
        $this->status = 'returned';
        $this->save();

        // Increment book availability
        $this->book->incrementAvailable();

        // Create fine if overdue
        if ($this->days_overdue > 0) {
            Fine::create([
                'transaction_id' => $this->id,
                'days_overdue' => $this->days_overdue,
                'amount_per_day' => 500,
                'total_amount' => $this->calculateFineAmount(),
            ]);
        }
    }

    /**
     * Scope for active (not returned) transactions
     */
    public function scopeActive($query)
    {
        return $query->whereNull('returned_at');
    }

    /**
     * Scope for overdue transactions
     */
    public function scopeOverdue($query)
    {
        return $query->whereNull('returned_at')
            ->where('due_date', '<', now());
    }

    /**
     * Scope for returned transactions
     */
    public function scopeReturned($query)
    {
        return $query->whereNotNull('returned_at');
    }
}
