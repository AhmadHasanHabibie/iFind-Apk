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
        'price_per_pax_snapshot',
        'dp_percentage_snapshot',
        'total_amount',
        'amount_due',
        'remaining_payment_status',
        'remaining_payment_method',
        'remaining_proof_path',
        'remaining_uploaded_at',
        'remaining_verified_at',
        'remaining_verified_by',
        'remaining_rejection_reason',
        'remaining_amount_received',
        'payment_proof_path',
        'payment_deadline',
        'payment_uploaded_at',
        'payment_verified_at',
        'payment_verified_by',
        'payment_rejection_reason',
        'qr_token',
        'checked_in_at',
        'checked_in_by',
        'refund_note',
        'refunded_at',
        'refunded_by',
    ];

    protected $casts = [
        'booking_date' => 'date',
        'seat_count' => 'integer',
        'confirmed_at' => 'datetime',
        'price_per_pax_snapshot' => 'decimal:2',
        'dp_percentage_snapshot' => 'integer',
        'total_amount' => 'decimal:2',
        'amount_due' => 'decimal:2',
        'remaining_uploaded_at' => 'datetime',
        'remaining_verified_at' => 'datetime',
        'remaining_amount_received' => 'decimal:2',
        'payment_deadline' => 'datetime',
        'payment_uploaded_at' => 'datetime',
        'payment_verified_at' => 'datetime',
        'checked_in_at' => 'datetime',
        'refunded_at' => 'datetime',
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

    public function verifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payment_verified_by');
    }

    public function remainingVerifiedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'remaining_verified_by');
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function refundedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }

    public function getRemainingAmountAttribute(): float
    {
        return round($this->total_amount - $this->amount_due, 2);
    }

    public function getIsPaidInFullAttribute(): bool
    {
        return (float) $this->amount_due >= (float) $this->total_amount;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'awaiting_payment' => 'Menunggu Pembayaran',
            'pending_verification' => 'Menunggu Verifikasi Toko',
            'confirmed' => 'Terkonfirmasi',
            'checked_in' => 'Sudah Check-in',
            'completed' => 'Selesai',
            'rejected_invalid_payment' => 'Ditolak (Pembayaran Tidak Valid)',
            'rejected_store_full' => 'Ditolak (Tempat Penuh)',
            'refunded' => 'Dana Telah Dikembalikan',
            'cancelled_expired' => 'Dibatalkan (Waktu Habis)',
            default => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
