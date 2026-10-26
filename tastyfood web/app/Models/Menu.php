<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    protected $table = 'menus';

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'category',
        'available',
        'sort_order',
        'order_count',
    ];

    protected $casts = [
        'price' => 'integer',
        'available' => 'boolean',
        'sort_order' => 'integer',
        'order_count' => 'integer',
    ];

    public function scopeAvailable($query)
    {
        return $query->where('available', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('order_count', 'desc');
    }

    public function scopeCategory($query, $category)
    {
        if ($category === 'semua' || empty($category)) return $query;
        return $query->where('category', $category);
    }

    public function scopeSearch($query, $search)
    {
        if (empty($search)) return $query;
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', '%' . $search . '%')
              ->orWhere('description', 'like', '%' . $search . '%');
        });
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getCategoryLabelAttribute()
    {
        return [
            'nasi' => 'Nasi',
            'lauk' => 'Lauk',
            'sayur' => 'Sayur',
            'minuman' => 'Minuman',
        ][$this->category] ?? ucfirst($this->category);
    }
}