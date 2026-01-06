<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rack extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'location',
        'capacity',
    ];

    /**
     * Get all books on this rack
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * Get current book count on this rack (unique titles)
     */
    public function getBookCountAttribute(): int
    {
        return $this->books()->count();
    }

    /**
     * Get total stock of all books on this rack
     */
    public function getTotalStockAttribute(): int
    {
        return (int) $this->books()->sum('stock');
    }

    /**
     * Check if rack is full based on total stock
     */
    public function getIsFullAttribute(): bool
    {
        return $this->total_stock >= $this->capacity;
    }
}
