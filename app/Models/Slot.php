<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Slot extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'date',
        'start_time',
        'end_time',
        'capacity',
        'booked_seats',
        'status',
    ];

    protected $casts = [
        'date' => 'date',
        'capacity' => 'integer',
        'booked_seats' => 'integer',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function getPendingSeatsAttribute(): int
    {
        return (int) $this->bookings()->where('status', 'pending')->sum('seat_count');
    }

    public function getEstimatedAvailableAttribute(): int
    {
        return max(0, $this->capacity - $this->booked_seats - $this->pending_seats);
    }
}
