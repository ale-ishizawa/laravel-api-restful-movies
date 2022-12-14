<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'file',
        'file_size',
    ];

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }
}
