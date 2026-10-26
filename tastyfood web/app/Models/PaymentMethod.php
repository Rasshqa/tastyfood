<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'account_name',
        'account_number',
        'qr_code',
        'instructions',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getTypeLabelAttribute()
    {
        return [
            'bank_transfer' => 'Bank Transfer',
            'qr_payment' => 'QR Payment',
            'e_wallet' => 'E-Wallet',
            'cash' => 'Cash',
        ][$this->type] ?? ucfirst($this->type);
    }
}
