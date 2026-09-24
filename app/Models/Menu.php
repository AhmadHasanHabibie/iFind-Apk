<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Menu extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'name',
        'category',
        'price',
        'description',
        'photo',
        'is_available',
        'is_recommended',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_available' => 'boolean',
        'is_recommended' => 'boolean',
    ];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getPhotoUrlAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }
        return '';
    }
}
