<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'order_number',
        'customer_name',
        'customer_email',
        'customer_phone',
        'delivery_method',
        'delivery_address',
        'status',
        'subtotal',
        'delivery_fee',
        'total',
        'notes',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'delivery_fee' => 'integer',
        'total' => 'integer',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function getFormattedTotalAttribute()
    {
        return 'Rp ' . number_format($this->total, 0, ',', '.');
    }

    public function getFormattedSubtotalAttribute()
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }

    public function getFormattedDeliveryFeeAttribute()
    {
        return 'Rp ' . number_format($this->delivery_fee, 0, ',', '.');
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Konfirmasi',
            'confirmed' => 'Dikonfirmasi',
            'preparing' => 'Diproses',
            'ready' => 'Siap',
            'in_transit' => 'Dalam Perjalanan',
            'delivered' => 'Selesai',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
        ];

        // For delivery orders, show "Dalam Perjalanan" when status is "in_transit"
        if ($this->delivery_method === 'delivery' && $this->status === 'ready') {
            return 'Siap Diantar';
        }

        return $labels[$this->status] ?? ucfirst($this->status);
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'bg-yellow-100 text-yellow-700',
            'confirmed' => 'bg-blue-100 text-blue-700',
            'preparing' => 'bg-indigo-100 text-indigo-700',
            'ready' => 'bg-green-100 text-green-700',
            'in_transit' => 'bg-purple-100 text-purple-700',
            'delivered' => 'bg-teal-100 text-teal-700',
            'completed' => 'bg-gray-100 text-gray-700',
            'cancelled' => 'bg-red-100 text-red-700',
        ][$this->status] ?? 'bg-gray-100 text-gray-700';
    }

    public static function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        $last = static::where('order_number', 'like', 'PR-' . $date . '%')
            ->orderBy('id', 'desc')
            ->first();
        
        if ($last) {
            $lastNumber = (int) substr($last->order_number, -4);
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return 'PR-' . $date . '-' . $newNumber;
    }
}