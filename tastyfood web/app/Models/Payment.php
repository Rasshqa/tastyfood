<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'payment_method_id',
        'user_id',
        'amount',
        'status',
        'proof_image',
        'user_notes',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected $casts = [
        'amount' => 'integer',
        'verified_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function verifier()
    {
        return $this->belongsTo(Admin::class, 'verified_by');
    }

    public function getFormattedAmountAttribute()
    {
        return 'Rp ' . number_format($this->amount, 0, ',', '.');
    }

    public function getStatusLabelAttribute()
    {
        return [
            'unpaid' => 'Belum Dibayar',
            'waiting_verification' => 'Menunggu Verifikasi',
            'paid' => 'Lunas',
            'rejected' => 'Ditolak',
            'expired' => 'Kadaluarsa',
        ][$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        return [
            'unpaid' => 'bg-gray-100 text-gray-700',
            'waiting_verification' => 'bg-yellow-100 text-yellow-700',
            'paid' => 'bg-green-100 text-green-700',
            'rejected' => 'bg-red-100 text-red-700',
            'expired' => 'bg-gray-100 text-gray-500',
        ][$this->status] ?? 'bg-gray-100 text-gray-700';
    }

    public function scopeWaitingVerification($query)
    {
        return $query->where('status', 'waiting_verification');
    }
}
