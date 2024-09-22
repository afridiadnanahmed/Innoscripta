<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class News extends Model
{
    use HasFactory;

    // Add the fillable fields
    protected $fillable = [
        'title',
        'description',
        'url',
        'source',
        'published_at',
    ];

        /**
     * The attributes that should be cast to dates.
     *
     * @var array
     */
    protected $dates = ['published_at'];
}
