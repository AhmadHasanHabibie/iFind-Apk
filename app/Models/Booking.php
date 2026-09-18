<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'store_id',
        'slot_id',
        'booking_date',
        'seat_count',
        'status',
        'notes',
        'rejection_reason',
        'confirmed_at',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'seat_count' => 'integer',
        'confirmed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function slot(): BelongsTo
    {
        return $this->belongsTo(Slot::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
