<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'rack_id',
        'title',
        'author',
        'isbn',
        'publisher',
        'year',
        'stock',
        'available',
        'cover_image',
        'description',
    ];

    protected $casts = [
        'year' => 'integer',
        'stock' => 'integer',
        'available' => 'integer',
    ];

    /**
     * Get the category of this book
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the rack location of this book
     */
    public function rack(): BelongsTo
    {
        return $this->belongsTo(Rack::class);
    }

    /**
     * Get all transactions for this book
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Check if book is available for borrowing
     */
    public function getIsAvailableAttribute(): bool
    {
        return $this->available > 0;
    }

    /**
     * Decrement available count when borrowed
     */
    public function decrementAvailable(): void
    {
        if ($this->available > 0) {
            $this->decrement('available');
        }
    }

    /**
     * Increment available count when returned
     */
    public function incrementAvailable(): void
    {
        if ($this->available < $this->stock) {
            $this->increment('available');
        }
    }

    /**
     * Scope for available books
     */
    public function scopeAvailable($query)
    {
        return $query->where('available', '>', 0);
    }

    /**
     * Scope for books by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }
}
