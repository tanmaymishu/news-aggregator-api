<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Preference extends Model
{
    protected $fillable = [
        'user_id',
        'sources',
        'categories',
        'authors',
    ];

    protected $casts = [
        'sources' => 'json',
        'categories' => 'json',
        'authors' => 'json',
    ];
}
