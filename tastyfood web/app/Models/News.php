<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class News extends Model
{
    use HasFactory;

    protected $table = 'news';
    protected $fillable = ['title', 'slug', 'image', 'content', 'author', 'status', 'published_at'];
    protected $casts = ['published_at' => 'datetime'];

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNotNull('published_at');
    }
}
