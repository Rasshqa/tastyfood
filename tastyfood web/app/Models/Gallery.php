<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Gallery extends Model
{
    use HasFactory;

    protected $table = 'galleries';
    protected $fillable = ['title', 'image', 'description', 'category', 'status', 'sort_order'];
}
