<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'name',
        'slug',
        'description',
        'address',
        'city',
        'latitude',
        'longitude',
        'phone',
        'opening_hours',
        'status',
        'rejection_reason',
        'is_active',
        'average_rating',
        'price_per_pax',
        'dp_percentage',
        'payment_timeout_minutes',
        'bank_name',
        'bank_account_number',
        'bank_account_holder',
        'qris_image_path',
    ];

    protected $casts = [
        'opening_hours' => 'array',
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'average_rating' => 'decimal:1',
        'price_per_pax' => 'decimal:2',
        'dp_percentage' => 'integer',
        'payment_timeout_minutes' => 'integer',
    ];

    public function canAcceptBookings(): bool
    {
        $hasPrice = ! is_null($this->price_per_pax) && $this->price_per_pax > 0;
        $hasPaymentMethod = (! empty($this->bank_name) && ! empty($this->bank_account_number)) || ! empty($this->qris_image_path);

        return $hasPrice && $hasPaymentMethod;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(StorePhoto::class);
    }

    public function slots(): HasMany
    {
        return $this->hasMany(Slot::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function facilities(): BelongsToMany
    {
        return $this->belongsToMany(Facility::class, 'facility_store')->withTimestamps();
    }

    public function getTodaySlotsAttribute()
    {
        return $this->slots()->whereDate('date', today())->orderBy('start_time')->get();
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 'approved')->where('is_active', true);
    }

    public function getPrimaryPhotoUrlAttribute(): string
    {
        $primary = $this->photos()->where('is_primary', true)->first() ?? $this->photos()->first();
        if ($primary && $primary->photo_path) {
            return asset('storage/' . $primary->photo_path);
        }
        return '';
    }
}
